<?php

namespace Arcana\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SaveController extends Controller
{
    /**
     * @Route("/save", name="arcana_content_save")
     * @Method("PUT")
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $json = $request->get('contents');

        if (!$json) {
            return new JsonResponse(array(
                'status' => 'error',
            ), 400);
        }

        $em = $this->getDoctrine()->getEntityManager();

        $texts = json_decode($json, true);

        $this->get('arcana.content.manager')
                ->updateTexts($texts);

        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok',
        ), 200);
    }
}
