<?php
/**
 * @author Donii Sergii <s.doniy@infomir.com>.
 */

namespace sonrac\Swagger\Tests\Functional;

use Silex\WebTestCase;

/**
 * Class TestSwaggerUI.
 *
 * @author Donii Sergii <s.donii@infomir.com>
 */
class TestSwaggerUI extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        return require __DIR__.'/../app/app.php';
    }
}
