<?php

namespace Labstag\DataListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Labstag\Entity\Email;
use Labstag\Entity\Template;
use Labstag\Entity\User;
use Labstag\Lib\EventSubscriberLib;
use Labstag\Repository\TemplateRepository;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class EmailListener extends EventSubscriberLib
{

    /**
     * @var RouterInterface|Router
     */
    protected $router;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, RouterInterface $router)
    {
        $this->container = $container;
        $this->router    = $router;
    }

    /**
     * Sur quoi écouter.
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Email) {
            return;
        }

        $this->checkEmail($entity, $args);
    }

    private function checkEmail(Email $entity, LifecycleEventArgs $args): void
    {
        $check = $entity->isChecked();
        if (true === $check) {
            return;
        }

        $search  = ['code' => 'checked-mail'];
        $manager = $args->getEntityManager();
        /** @var TemplateRepository $repository */
        $repository = $manager->getRepository(Template::class);
        /** @var Template $template */
        $template = $repository->findOneBy($search);
        $html     = $template->getHtml();
        $text     = $template->getText();
        /** @var User $user */
        $user = $entity->getRefuser();
        $this->setConfigurationParam($args);
        $replace = [
            '%site%'     => $this->configParams['site_title'],
            '%username%' => $user->getUsername(),
            '%email%'    => $entity->getAdresse(),
            '%url%'      => $this->router->generate(
                'check-email',
                [
                    'id' => $entity->getId(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        $html    = strtr($html, $replace);
        $text    = strtr($text, $replace);
        $message = new Swift_Message();
        $sujet   = str_replace(
            '%site%',
            $this->configParams['site_title'],
            $template->getname()
        );
        $message->setSubject($sujet);
        $message->setFrom($user->getEmail());
        $message->setTo($this->configParams['site_no-reply']);
        $message->setBody($html, 'text/html');
        $message->addPart($text, 'text/plain');
        /** @var Swift_Mailer $mailer */
        $mailer = $this->container->get('swiftmailer.mailer.default');
        $mailer->send($message);
    }
}
