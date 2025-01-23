<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): Response
    {
        // Message personnalisé pour les accès refusés
        return new Response('Access denied: You must log in to access this page.', 403);
    }
}
