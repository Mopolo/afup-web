<?php

declare(strict_types=1);

namespace Afup\Tests\Support;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Classe de base pour les tests d'intégration.
 *
 * Ces tests ont accès à la base de données de test vide et reset à chaque test.
 *
 * Il est également possible dans ces tests de récupérer des services depuis le container Symfony.
 */
abstract class IntegrationTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();

        $databaseManager = new DatabaseManager(false);
        $databaseManager->reloadDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->restoreExceptionHandler();
    }

    protected function restoreExceptionHandler(): void
    {
        while (true) {
            $previousHandler = set_exception_handler(static fn(): null => null);

            restore_exception_handler();

            if ($previousHandler === null) {
                break;
            }

            restore_exception_handler();
        }
    }
}
