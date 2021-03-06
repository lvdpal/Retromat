<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/{_locale}/", requirements={"_locale": "en|de|fr|es|nl"}, name="activities_by_id")
     */
    public function homeAction(Request $request)
    {
        $ids = explode('-', $request->query->get('id'));
        $phase = $request->query->get('phase');

        $activities = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Activity')->findOrdered(
            $request->getLocale(),
            $ids
        );

        return $this->render(
            'home/generated/index_'.$request->getLocale().'.html.twig',
            [
                'ids' => $ids,
                'phase' => $phase,
                'activities' => $activities,
                'color_variation' => $this->get('retromat.color_varation'),
                'activity_by_phase' => $this->get('retromat.activity_by_phase'),
                'activity_source' => $this->getParameter('retormat.activity.source'),
            ]
        );
    }

    /**
     * @Route("/", defaults={"_locale": "en"}, name="home_slash")
     * @Route("/index.html", defaults={"_locale": "en"}, name="home_index")
     * @Route("/index_{_locale}.html", requirements={"_locale": "en|de|fr|es|nl"}, name="home")
     */
    public function redirectAction(Request $request)
    {
        return $this->redirectToRoute(
            'activities_by_id',
            ['id' => $request->query->get('id'), 'phase' => $request->query->get('phase')],
            301
        );
    }
}