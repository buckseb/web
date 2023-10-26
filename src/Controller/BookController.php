<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/addBook', name: 'addBook')]
    public function addBook(Request $request, ManagerRegistry $managerRegistry)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        $book->setpublished(true);
        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $nbBooks=$book->getAuthor()->getnbrBooks();

            $em->persist($book);
            $em->flush();
            return new Response("done");
        }
        return $this->render("book/addbook.html.twig", array("formulaireBook" => $form->createView()));
    }

    #[Route('/bookList', name: 'book_list')]
    public function list(BookRepository $repository)
    {
        $book= $repository->findAll();
        return $this->render("book/list.html.twig",
            array("tabBooks"=>$book));
    }

    #[Route('/editbook/{ref}', name: 'edit_book')]
    public function edit(Request $request, ManagerRegistry $managerRegistry,BookRepository $repository,$ref)
    {
        $book = $repository->find($ref);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $em->flush();
            return new Response("Book modified");
        }
        return $this->render("book/edit.html.twig", array("formulaireBook" => $form->createView()));
    }
    #[Route('/deletebook/{ref}', name: 'delete_book')]
    public function delete($ref,BookRepository $repository,ManagerRegistry $managerRegistry)
    {
        $book= $repository->find($ref);
        $em= $managerRegistry->getManager();
        $em->remove($book);
        $em->flush();
        return new Response("Book deleted");
    }
    #[Route('/showbook/{ref}', name: 'show_book')]
    public function show($ref,BookRepository $repository){
        $book= $repository->find($ref);
        return $this->render('book/show.html.twig',array('thisBook'=>$book));
    }
}
