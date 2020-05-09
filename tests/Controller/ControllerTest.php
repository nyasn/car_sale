<?php

namespace Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HTTPFoundation\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;

class ControllerTest extends WebTestCase
{
  //Test Register User
  public function testRegister()
  {
    $client = static::createClient();

    $crawler = $client->request('GET', '/create-account');

    $form = $crawler->selectButton('Enregistrer')->form();

    $form['registration_form[email]'] = 'toto@email.com';
    $form['registration_form[username]'] = 'usernametest';
    $form['registration_form[first_name]'] = 'John Doe';
    $form['registration_form[last_name]'] = 'John Doe';
    $form['registration_form[address]'] = 'John Doe';
    $form['registration_form[phone]'] = 'John Doe';
    $form['registration_form[plainPassword][first]'] = 'pass1';
    $form['registration_form[plainPassword][second]'] = 'pass2';

    $crawler = $client->submit($form);
    $this->assertEquals(1,
        $crawler->filter('li:contains("Les deux mots de passe ne sont pas identiques.")')->count());
  }


  //Test Login User
  public function testLogin()
  {
    $client = static::createClient();

    $crawler = $client->request('GET', '/connexion');

    $form = $crawler->selectButton('Login')->form(array(
      '_username'  => 'usernametest',
      '_password'  => 'pass1',
      ));

    $crawler = $client->submit($form);
    $this->assertTrue($client->getResponse()->isRedirect());
    $crawler = $client->followRedirect();
  }


  //Test de page index d'admin
  public function testIndexAdminAction()
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/admin');

    $this->assertEquals(
      Response::HTTP_FOUND,
      $client->getResponse()->getStatusCode()
    );
  }


  //Test ajouter Produit
  public function testAddProduct()
  {
    $client = static::createClient();
    $crawler = $client->request(
      'POST',
      "/admin/product/add",
      array('titre' => 'Mazda', 
        'prix' => 123,
        'description' => 'Si un produit est compliqué à prononcer, il sera difficile de le trouver en ligne.',
        'qte' => 230,
        'genre' => 'Mazda',
        'fournisseur' => 1
      ),
      array(),
      array('HTTP_Content-Type' => 'application/json')
    );
    $this->assertEquals(
      Response::HTTP_FOUND,
      $client->getResponse()->getStatusCode()
    );
  }

  //Test ajouter fournisseur
  public function testAddFournisseur()
  {
    $client = static::createClient();
    $crawler = $client->request(
      'POST',
      "/admin/fournisseur/add",
      array('name' => 'Mazda',
        'description' => 'Si un produit est compliqué à prononcer, il sera difficile de le trouver en ligne.'
      ),
      array(),
      array('HTTP_Content-Type' => 'application/json')
    );
    $this->assertEquals(
      Response::HTTP_FOUND,
      $client->getResponse()->getStatusCode()
    );
  }



}