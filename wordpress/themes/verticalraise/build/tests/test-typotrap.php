<?php
/**
 * Class TypoTrap
 *
 * @package Vertical_Raise_Theme_Buildout
 */
use \classes\app\emails\typo_trap\TypoTrap;
/**
 * Test case.
 */


class TypoTrapTest extends WP_UnitTestCase {

    /**
     * Test if roles are detected
     */
    public function test_roles() {

        $t = new TypoTrap();

        $this->assertFalse( $t->check("john@hotmail.com") );
        $this->assertFalse( $t->check("john@hot.mail.com") );
        $this->assertTrue( $t->check("mail@hotmail.com") );
        $this->assertTrue( $t->check("mail@hot.mail.com") );
        $this->assertTrue( $t->check("admin@hotmail.com") );

    }

    public function test_blocked_domains(){
        $t = new TypoTrap();

        $this->assertTrue( $t->check('john.smith@freeemails.tk'));
        $this->assertTrue( $t->check('mark.two0002@10minutemail.click'));
        $this->assertTrue( $t->check('fih.20129@temporaryemail.gq'));
        $this->assertTrue( $t->check('john.smith@freeemails.cf'));
        $this->assertTrue( $t->check('mark.two0002@10minutemail.ml'));
        $this->assertTrue( $t->check('fih.20129@temporaryemail.ga'));
        $this->assertTrue( $t->check('john.smith@freeemails.loan'));
        $this->assertTrue( $t->check('mark.two0002@10minutemail.men'));
        $this->assertTrue( $t->check('fih.20129@temporaryemail.date'));
    }

    public function test_global_typo(){
        $t = new TypoTrap();

        $this->assertTrue( $t->check( 'tomas@yandex.net' ) );
        $this->assertTrue( $t->check( 'karina_lopes@yahoo.nt' ) );
        $this->assertTrue( $t->check( 'luana_brasil@protonmail.nt' ) );
        $this->assertTrue( $t->check( 'mariomasachessi2017@live.co' ) );
        $this->assertTrue( $t->check( 'john_addams20@gmail.cm' ));

    }

    public function test_typo (){
        $t = new TypoTrap();

        $this->assertTrue( $t->check( 'lucasjalise2017@gmaul.com' ) );
        $this->assertTrue( $t->check( 'jujoskl@oulook.com' ) );
        $this->assertTrue( $t->check( 'lopezsonrad@yayoo.com' ) );

    }

}
