<?php
// @codingStandardsIgnoreStart
namespace MethodClassReflectionReturnTypesNamespace;

// SilverStripe
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataObject;
use Syntro\SilverstripePHPStan\ClassHelper;
use function PHPStan\Testing\assertType;

/**
 * setting up relations in the Classes doesn't get picked up
 * Need to add to the config manually
 * @return void
 */
function initDbRelations() {
    Config::modify()->merge(Team::class, 'has_many', [
        'Players' => Player::class,
        'WinningPlayers' => Player::class . '.WinningTeam'
    ]);

    Config::modify()->merge(Player::class, 'has_one', [
        'Team' => Team::class,
        'WinningTeam' => Team::class . '.WinningPlayers'
    ]);

    Config::modify()->merge(Coach::class, 'belongs_to', [
        'Team' => Team::class,
        'WinningTeam' => Team::class . '.WinningCoach',
    ]);
}

initDbRelations();

class Foo
{
    public function doFoo(): void
    {


        $player = new Player;
        // Standard has_one
        assertType(Team::class, $player->Team());
        // Has one with a custom relation name
        assertType(Team::class, $player->WinningTeam());

        $team = new Team();
        // Standard has_one
        assertType( sprintf('%s<%s>', ClassHelper::HasManyList, Player::class), $team->Players());
        // Has one with a custom relation name
        assertType( sprintf('%s<%s>', ClassHelper::HasManyList, Player::class), $team->WinningPlayers());

        $coach = new Coach();
        assertType(Team::class, $coach->Team());
        // Has one with a custom relation name
        assertType(Team::class, $coach->WinningTeam());
        die();
    }
}

class Team extends DataObject
{

}

class Player extends DataObject
{
}

class Coach extends DataObject
{
}
// @codingStandardsIgnoreEnd
