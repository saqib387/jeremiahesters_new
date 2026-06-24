<?php

namespace App;

/**
 * Alias class for App\Model\User
 * This is needed because some parts of the codebase reference App\User
 * but the actual User model is in App\Model\User
 */
class User extends \App\Model\User
{
    // This class extends App\Model\User to provide backward compatibility
    // for code that references App\User
}
