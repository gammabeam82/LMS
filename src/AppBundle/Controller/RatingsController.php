<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\RatingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RatingsController extends Controller
{
    /**
     * @Route("/books/{id}/rating", name="books_rating")
     * @ParamConverter("book")
     *
     * @param Request $request
     * @param Book $book
     * @return RedirectResponse|Response
     */
    public function formAction(Request $request, Book $book)
    {
        $ratingService = $this->get('app.ratings');

        $rating = $ratingService->getRating($book, $this->getUser());

        $ratingForm = $this->createForm(RatingType::class, $rating, [
            'action' => $this->generateUrl('books_rating', [
                'id' => $book->getId()
            ])
        ]);

        $ratingForm->handleRequest($request);

        if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
            $translator = $this->get('translator');

            $ratingService->save($this->getUser(), $book, $rating);

            $this->addFlash('notice', $translator->trans('messages.vote_success'));

            return $this->redirectToRoute('books_view', [
                'id' => $book->getId()
            ]);
        }

        return $this->render('ratings/form.html.twig', [
            'form' => $ratingForm->createView(),
            'rating' => $rating
        ]);
    }
}
