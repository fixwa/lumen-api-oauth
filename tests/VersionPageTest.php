<?php

class VersionPageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExampleVersionPage()
    {
        $this->get('/version');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    public function testVersionPage()
    {

        $response = $this->call('GET', '/version');
        $this->assertResponseOk();
    }
}
