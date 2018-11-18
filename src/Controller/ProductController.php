<?php
    namespace App\Controller;

    use App\Entity\Product;
    use App\Entity\User;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    
    
    class ProductController extends Controller {
        /**
         * @Route("/", name="product_list")
         * @Method("GET")
         */
        public function index(){
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
            return $this->render('products/index.html.twig',array('products' => $products));
        }

         /**
         * @Route("/product/{id}")
         */
        public function show($id) {
            $tokenInterface = $this->get('security.token_storage')->getToken();
            $isAuthenticated = $tokenInterface->isAuthenticated();
            $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
            return $this->render('products/show.html.twig', array('product' => $product));
        }

        


      
       
    }
?>