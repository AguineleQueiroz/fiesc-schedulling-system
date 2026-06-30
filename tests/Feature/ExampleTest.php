<?php

it('unauthenticated access to root redirects to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
