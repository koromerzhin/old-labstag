<?php

namespace Labstag\Controller\Admin;

use Labstag\Entity\Category;
use Labstag\Entity\Post;
use Labstag\Entity\Tags;
use Labstag\Form\Admin\CategoryType;
use Labstag\Form\Admin\PostType;
use Labstag\Form\Admin\TagsType;
use Labstag\Lib\AdminControllerLib;
use Labstag\Repository\CategoryRepository;
use Labstag\Repository\PostRepository;
use Labstag\Repository\TagsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/post")
 */
class PostAdmin extends AdminControllerLib
{
    /**
     * @Route("/", name="adminpost_list", methods={"GET"})
     * @Route("/trash", name="adminpost_trash", methods={"GET"})
     */
    public function list(PostRepository $repository): Response
    {
        $datatable = [
            'Name'      => ['field' => 'name'],
            'User'      => [
                'field'     => 'refuser',
                'formatter' => 'dataFormatter',
            ],
            'Categorie' => [
                'field'     => 'refcategory',
                'formatter' => 'dataFormatter',
            ],
            'File'      => [
                'field'     => 'file',
                'formatter' => 'imageFormatter',
            ],
            'Enable'    => [
                'field'     => 'enable',
                'formatter' => 'enableFormatter',
                'url'       => $this->generateUrl('adminpost_enable'),
            ],
            'CreatedAt' => [
                'field'     => 'createdAt',
                'formatter' => 'dateFormatter',
            ],
            'UpdatedAt' => [
                'field'     => 'updatedAt',
                'formatter' => 'dateFormatter',
            ],
        ];
        $data      = [
            'title'           => 'Post list',
            'datatable'       => $datatable,
            'repository'      => $repository,
            'api'             => 'api_posts_get_collection',
            'url_delete'      => 'adminpost_delete',
            'url_deletetrash' => 'adminpost_deletetrash',
            'url_trash'       => 'adminpost_trash',
            'url_list'        => 'adminpost_list',
            'url_edit'        => 'adminpost_edit',
            'url_trashedit'   => 'adminpost_trashedit',
        ];

        $categories = $repository->findAll();
        if (count($categories)) {
            $data['url_new'] = 'adminpost_new';
        } else {
            $this->addFlash('warning', 'Vous ne pouvez pas créer de post sans créer de catégories');
        }

        return $this->crudListAction($data);
    }

    /**
     * @Route("/enable", name="adminpost_enable")
     */
    public function enable(PostRepository $repository): JsonResponse
    {
        return $this->crudEnableAction($repository, 'setEnable');
    }

    /**
     * @Route("/new", name="adminpost_new", methods={"GET", "POST"})
     */
    public function new(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        if (0 == count($categories)) {
            throw new HttpException(403, 'Vous ne pouvez pas créer de post sans créer de catégories');
        }

        return $this->crudNewAction(
            [
                'entity'   => new Post(),
                'form'     => PostType::class,
                'url_edit' => 'adminpost_edit',
                'url_list' => 'adminpost_list',
                'title'    => 'Add new post',
            ]
        );
    }

    /**
     * @Route("/edit/{id}", name="adminpost_edit", methods={"GET", "POST"})
     */
    public function edit(Post $post, CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        if (0 == count($categories)) {
            throw new HttpException(403, 'Vous ne pouvez pas créer de post sans créer de catégories');
        }

        return $this->crudEditAction(
            [
                'form'       => PostType::class,
                'entity'     => $post,
                'url_list'   => 'adminpost_list',
                'url_edit'   => 'adminpost_edit',
                'url_delete' => 'adminpost_delete',
                'title'      => 'Edit post',
            ]
        );
    }

    /**
     * @Route("/", name="adminpost_delete", methods={"DELETE"})
     * @Route("/trash", name="adminpost_deletetrash", methods={"DELETE"})
     */
    public function delete(PostRepository $repository): JsonResponse
    {
        return $this->crudDeleteAction(
            $repository,
            [
                'url_list'  => 'adminpost_list',
                'url_trash' => 'adminpost_trash',
            ]
        );
    }

    /**
     * @Route("/category/", name="adminpostcategory_list", methods={"GET"})
     * @Route("/category/trash", name="adminpostcategory_trash", methods={"GET"})
     */
    public function listCategory(CategoryRepository $repository): Response
    {
        $datatable = [
            'Name'      => ['field' => 'name'],
            'Posts'     => [
                'field'     => 'posts',
                'formatter' => 'dataTotalFormatter',
            ],
            'CreatedAt' => [
                'field'     => 'createdAt',
                'formatter' => 'dateFormatter',
            ],
            'UpdatedAt' => [
                'field'     => 'updatedAt',
                'formatter' => 'dateFormatter',
            ],
        ];
        $data      = [
            'title'           => 'Category list',
            'datatable'       => $datatable,
            'repository'      => $repository,
            'api'             => 'api_categories_get_collection',
            'url_new'         => 'adminpostcategory_new',
            'url_delete'      => 'adminpostcategory_delete',
            'url_deletetrash' => 'adminpostcategory_deletetrash',
            'url_trash'       => 'adminpostcategory_trash',
            'url_list'        => 'adminpostcategory_list',
            'url_edit'        => 'adminpostcategory_edit',
            'url_trashedit'   => 'adminpostcategory_trashedit',
        ];

        return $this->crudListAction($data);
    }

    /**
     * @Route("/category/new", name="adminpostcategory_new", methods={"GET", "POST"})
     */
    public function newCategory(): Response
    {
        return $this->crudNewAction(
            [
                'entity'   => new Category(),
                'form'     => CategoryType::class,
                'url_edit' => 'adminpostcategory_edit',
                'url_list' => 'adminpostcategory_list',
                'title'    => 'Add new categorie',
            ]
        );
    }

    /**
     * @Route("/category/edit/{id}", name="adminpostcategory_edit", methods={"GET", "POST"})
     */
    public function editCategory(Category $category): Response
    {
        return $this->crudEditAction(
            [
                'form'       => CategoryType::class,
                'entity'     => $category,
                'url_list'   => 'adminpostcategory_list',
                'url_edit'   => 'adminpostcategory_edit',
                'url_delete' => 'adminpostcategory_delete',
                'title'      => 'Edit categorie',
            ]
        );
    }

    /**
     * @Route("/category/", name="adminpostcategory_delete", methods={"DELETE"})
     * @Route("/category/trash", name="adminpostcategory_deletetrash", methods={"DELETE"})
     */
    public function deleteCategory(CategoryRepository $repository): JsonResponse
    {
        return $this->crudDeleteAction(
            $repository,
            [
                'url_list'  => 'adminpostcategory_list',
                'url_trash' => 'adminpostcategory_trash',
            ]
        );
    }

    /**
     * @Route("/tags/", name="adminposttags_list", methods={"GET"})
     * @Route("/tags/trash", name="adminposttags_trash", methods={"GET"})
     */
    public function listTags(TagsRepository $repository): Response
    {
        $datatable = [
            'Name'      => ['field' => 'name'],
            'Posts'     => [
                'field'     => 'posts',
                'formatter' => 'dataTotalFormatter',
            ],
            'CreatedAt' => [
                'field'     => 'createdAt',
                'formatter' => 'dateFormatter',
            ],
            'UpdatedAt' => [
                'field'     => 'updatedAt',
                'formatter' => 'dateFormatter',
            ],
        ];
        $data      = [
            'title'           => 'Tags list',
            'datatable'       => $datatable,
            'repository'      => $repository,
            'api'             => 'api_tags_get_collection',
            'url_new'         => 'adminposttags_new',
            'url_delete'      => 'adminposttags_delete',
            'url_deletetrash' => 'adminposttags_deletetrash',
            'url_trash'       => 'adminposttags_trash',
            'url_list'        => 'adminposttags_list',
            'url_edit'        => 'adminposttags_edit',
            'url_trashedit'   => 'adminposttags_trashedit',
        ];

        return $this->crudListAction($data);
    }

    /**
     * @Route("/tags/new", name="adminposttags_new", methods={"GET", "POST"})
     */
    public function newTags(): Response
    {
        return $this->crudNewAction(
            [
                'entity'   => new Tags(),
                'form'     => TagsType::class,
                'url_edit' => 'adminposttags_edit',
                'url_list' => 'adminposttags_list',
                'title'    => 'Add new tag',
            ]
        );
    }

    /**
     * @Route("/tags/edit/{id}", name="adminposttags_edit", methods={"GET", "POST"})
     */
    public function editTags(Tags $tag): Response
    {
        return $this->crudEditAction(
            [
                'form'       => TagsType::class,
                'entity'     => $tag,
                'url_list'   => 'adminposttags_list',
                'url_edit'   => 'adminposttags_edit',
                'url_delete' => 'adminposttags_delete',
                'title'      => 'Edit tag',
            ]
        );
    }

    /**
     * @Route("/tags/", name="adminposttags_delete", methods={"DELETE"})
     * @Route("/tags/trash", name="adminposttags_deletetrash", methods={"DELETE"})
     */
    public function deleteTags(TagsRepository $repository): JsonResponse
    {
        return $this->crudDeleteAction(
            $repository,
            [
                'url_list'  => 'adminposttags_list',
                'url_trash' => 'adminposttags_trash',
            ]
        );
    }
}
