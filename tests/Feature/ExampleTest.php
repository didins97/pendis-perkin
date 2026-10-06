<?php

test('a guest is redirected to sign in from the root route', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('signin'));
});
