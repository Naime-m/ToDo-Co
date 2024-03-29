<?php

namespace Tests\App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testLoginPageIsUp()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@local.com');
        $client->loginUser($testUser);
        $client->request('GET', '/login');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testUserCreatePageIsUp()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@local.com');
        $client->loginUser($testUser);
        $client->request('GET', '/users/create');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testUserListPageIsUp()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('testadmin@local.com');
        $client->loginUser($testUser);
        $client->request('GET', '/users');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testUserEditIsUp()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('testadmin@local.com');
        $client->loginUser($testUser);
        $client->request('GET', '/users/1/edit');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testUserCreate()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@local.com');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'John Doe';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'johndoe@gmail.local';
        $form['user[role]'] = 'ROLE_ADMIN';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("L\'utilisateur a bien été ajouté")')->count());
    }

    public function testUserCantAccessAdminSection()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@local.com');
        $client->loginUser($testUser);
        $client->request('GET', '/users');
        $this->assertSame(403, $client->getResponse()->getStatusCode());
    }

    public function testUserEditRole()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('testadmin@local.com');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/users/3/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'testuser';
        $form['user[password][first]'] = 'test';
        $form['user[password][second]'] = 'test';
        $form['user[email]'] = 'testuser@gmail.local';
        $form['user[role]'] = 'ROLE_ADMIN';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("L\'utilisateur a bien été modifié")')->count());
    }
}
