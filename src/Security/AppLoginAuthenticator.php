<?php

namespace App\Security;

use App\Entity\FavouriteOrder;
use App\Entity\ProductOrder;
use App\Repository\CartRepository;

use App\Repository\FavouriteRepository;
use App\Repository\ProductChoiceRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Service\FavouriteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppLoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private CartService $cartService;
    private CartRepository $cartRepository;
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private ProductChoiceRepository $productChoiceRepository;
    private FavouriteService $favouriteService;
    private FavouriteRepository $favouriteRepository;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        CartService $cartService,
        FavouriteService $favouriteService,
        CartRepository $cartRepository,
        FavouriteRepository $favouriteRepository,
        ProductRepository $productRepository,
        ProductChoiceRepository $productChoiceRepository,
        EntityManagerInterface $entityManager)
    {
        $this->cartService = $cartService;
        $this->cartRepository = $cartRepository;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->productChoiceRepository = $productChoiceRepository;
        $this->favouriteService = $favouriteService;
        $this->favouriteRepository = $favouriteRepository;
    }

//    public function supports(Request $request): bool
//    {
//        if($request->getPathInfo() === '/login' && $request->isMethod('POST')){
//            return true;
//        }
//        return false;
//    }

    public function authenticate(Request $request): Passport
    {

        $email = $request->request->get('email', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        if($sessionCart = $this->cartService->getSessionCart()){

            $cart = $this->cartRepository->findOneBy(['user' => $user]);

            foreach ($sessionCart->getProductOrders() as $sessionCartProductOrder){
                $product = $sessionCartProductOrder->getProduct();
                $productChoice = $sessionCartProductOrder->getProductChoice();

                $quantity = $sessionCartProductOrder->getQuantity();
                $inCart = false;
                foreach ($cart->getProductOrders() as $cartProductOrder){
                    if(
                        $cartProductOrder->getProduct()->getId() === $product->getId() &&
                        $cartProductOrder->getProductChoice()?->getId() === $productChoice?->getId()
                    ){
                        $cartProductOrder->setQuantity($cartProductOrder->getQuantity() + $quantity);
                        $this->entityManager->persist($cartProductOrder);
                        $inCart = true;
                    }
                }

                if(!$inCart){
                    $productOrder = new ProductOrder();
                    $productOrder->setProduct($this->productRepository->findOneBy(['id' => $product->getId()]));
                    if($productChoice){
                        $productOrder->setProductChoice($this->productChoiceRepository->findOneBy(['id' => $productChoice->getId()]));
                    }

                    $productOrder->setQuantity($quantity);
                    $this->entityManager->persist($productOrder);
                    $cart->addProductOrder($productOrder);
                    $this->entityManager->persist($cart);
                }

            }
            $this->entityManager->flush();
        }

        if($sessionFavourite = $this->favouriteService->getSessionFavourite()){
            $favourite = $this->favouriteRepository->findOneBy(['user' => $user]);
            $favouriteOrders = $favourite->getFavouriteOrders();
            foreach ($sessionFavourite->getFavouriteOrders() as $favouriteOrderSession){
                $product = $favouriteOrderSession->getProduct();
                $productChoice = $favouriteOrderSession->getProductChoice();
                $inFavourite = false;

                foreach ($favouriteOrders as $favouriteOrder){

                    if(
                        $product->getId() === $favouriteOrder->getProduct()->getId() &&
                        $productChoice?->getId() === $favouriteOrder->getProductChoice()?->getId()
                    ){
                        $inFavourite = true;
                        break;
                    }
                }

                if(!$inFavourite){
                    $favouriteOrder = new FavouriteOrder();
                    $favouriteOrder->setProduct($this->productRepository->findOneBy(['id' => $product->getId()]));
                    if($productChoice){
                        $favouriteOrder->setProductChoice($this->productChoiceRepository->findOneBy(['id' => $productChoice->getId()]));
                    }
                    $this->entityManager->persist($favouriteOrder);
                    $favouriteOrder->setFavourite($favourite);
                    $this->entityManager->persist($favourite);
                }
            }

            $this->entityManager->flush();
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate('app_homepage'));

    }



    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
