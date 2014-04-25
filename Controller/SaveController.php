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

        $em = $this->getDoctrine()->getManager();

        $texts = json_decode($json, true);

        $this->getContentManager()->updateTexts($texts);

        $em->flush();

        $emptyVals = $this->getContentManager()->getEmptyVals();

        if(!empty($emptyVals)){
            return new JsonResponse(array(
                'status'    => 'partial',
                'emptyVals' => $emptyVals
            ), 206);
        }

        return new JsonResponse(array(
            'status' => 'ok',
        ), 200);
    }

    /**
     * @return ContentManager
     */
    private function getContentManager()
    {
        return $this->get('arcana.content.manager');
    }

}
