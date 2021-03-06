<?php

namespace Labstag\Controller;

use Labstag\Form\Front\ContactType;
use Labstag\Lib\ControllerLib;
use Labstag\Repository\TemplateRepository;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends ControllerLib
{
    /**
     * @Route("/", name="front")
     */
    public function index(): RedirectResponse
    {
        return $this->redirect(
            $this->generateUrl('api_entrypoint'),
            301
        );
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(TemplateRepository $repository, Swift_Mailer $mailer): Response
    {
        $search    = ['code' => 'contact'];
        $templates = $repository->findOneBy($search);
        if (!$templates) {
            throw new HttpException(403, "Erreur de base de données. Veuillez contacter l'administrateur");
        }

        $form = $this->createForm(
            ContactType::class,
            [],
            [
                'action' => $this->generateUrl('contact'),
            ]
        );
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $this->request->request->get('contact');
            $html    = $templates->getHtml();
            $text    = $templates->getText();
            $this->setConfigurationParam();
            $replace = [
                '%sujet%'   => $contact['sujet'],
                '%site%'    => $this->paramViews['config']['site_title'],
                '%name%'    => $contact['name'],
                '%email%'   => $contact['email'],
                '%message%' => $contact['content'],
            ];

            $html    = strtr((string) $html, $replace);
            $text    = strtr((string) $text, $replace);
            $message = new Swift_Message();
            $sujet   = $templates->getname();
            $sujet   = str_replace(
                '%site%',
                (string) $this->paramViews['config']['site_title'],
                (string) $sujet
            );
            $message->setSubject($sujet);
            $message->setFrom($this->paramViews['config']['site_email']);
            $message->setTo($this->paramViews['config']['site_no-reply']);
            $message->setBody($html, 'text/html');
            $message->addPart($text, 'text/plain');
            $mailer->send($message);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Message envoyé');
        }

        return $this->twig(
            'front/contact.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
