<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Controller\Frontend;

use Oro\Bundle\CheckoutBundle\Entity\Checkout;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxFundboxController extends Controller
{
    /**
     * @Route(
     *      "/get-order-details-for-checkout/{entityId}",
     *      name="fundbox_checkout_order_details",
     *      requirements={"entityId"="\d+"},
     *      options= {"expose"=true}
     * )
     *
     * @param Request $request
     * @param integer $entityId
     *
     * @return JsonResponse
     */
    public function getOrderDetailsAndPMAction(Request $request, $entityId)
    {
        /** @var Checkout $checkout */
        $checkout = $this->getDoctrine()->getManagerForClass(Checkout::class)
            ->getRepository(Checkout::class)->find($entityId);
        if (!$checkout) {
            return new JsonResponse('', Response::HTTP_NOT_FOUND);
        }
        $orderDetails = $this->get('fundbox_checkout.model.order_details')->getOrderDetailsArray($checkout);
        return new JsonResponse(['orderDetails' => $orderDetails, 'paymentMethod' => $checkout->getPaymentMethod()]);
    }
}
