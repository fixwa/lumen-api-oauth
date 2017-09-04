<?php

class AuthPagesTest extends TestCase
{
    /**
     * Tried to Logout with wrong authentication.
     *
     * @return void
     */
    public function testLogoutWithInvalidAuthentication()
    {
        // Try w/o Authorization Header.
        $this->delete('/logout');
        $this->assertEquals(400, $this->response->getStatusCode());

        // Try with a random Authorization Header.
        $this->delete('/logout', [], ['Authorization' => 'Bearer AAAAAAAAAAAAAAAAAAAAAAAAA']);
        $this->assertEquals(401, $this->response->getStatusCode());
        $this->assertContains('The resource owner or authorization server denied the request.', $this->response->getContent());
    }

    /**
     * Verifies that an authenticated user can logout with the proper Authorization Header.
     */
    public function testLogoutSuccessful()
    {
        $postData = [
            'client_id' => 'id0',
            'client_secret' => 'secret0',
            'username' => 'fixwah@gmail.com',
            'password' => '12345678',
            'grant_type' => 'password',
        ];
        $this->post('/oauth/access_token', $postData);
        $this->assertContains('access_token', $this->response->getContent());
        $this->assertResponseOk();

        $json = json_decode($this->response->getContent());
        $this->delete('/logout', [], ['Authorization' => 'Bearer ' . $json->access_token]);
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertContains('Goodbye.', $this->response->getContent());
    }
}
