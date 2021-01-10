<?php
/**
 * Class MailTest
 *
 * @package Vertical_Raise_Theme_Buildout
 */

/**
 * Mail test case.
 */
class MailTest extends WP_UnitTestCase {

    const TEST_EMAIL = 'kny20195@bcaoo2.com';

	/**
	 * Sendgrid API test
	 */
	public function test_sendgrid() {

	    $cm = new \classes\app\emails\Custom_Mail();

        $to            = self::TEST_EMAIL;
        $from          = 'phpunit@verticalraise.com';
        $cc            = '';
        $subject       = 'PHPUnit Test';
        $html_content  = '<h1>Hello</h1><p>PHPUnit Test</p><small>2019</small>';
        $plain_content = 'Hello\\nPhpUnit Test\\n2019';
        $from_name     = 'PHPUnit';
        $headers       = '';

	    $result = $cm->test_send_email_api($to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers );

		$this->assertTrue( $result );

	}

    /**
     * Sendgrid API test
     */
    public function test_sendgrid_minimal_fields() {

        $cm = new \classes\app\emails\Custom_Mail();

        $to            = self::TEST_EMAIL;
        $from          = 'phpunit@verticalraise.com';
        $subject       = 'PHPUnit Test';
        $html_content  = '<h1>Hello</h1><p>PHPUnit Test</p><small>2019</small>';

        $result = $cm->test_send_email_api($to, $from, null, $subject, $html_content , null, null, null);
        $this->assertTrue( $result );

    }

    /**
     * Sendgrid API exception test
     */
    public function test_sendgrid_exception_to() {

        $cm = new \classes\app\emails\Custom_Mail();

        $to            = '';
        $from          = 'phpunit@verticalraise.com';
        $cc            = '';
        $subject       = 'PHPUnit Test';
        $html_content  = '<h1>Hello</h1><p>PHPUnit Test</p><small>2019</small>';
        $plain_content = 'Hello\\nPhpUnit Test\\n2019';
        $from_name     = 'PHPUnit';
        $headers       = '';

        $this->expectException(\SendGrid\Mail\TypeException::class);
        $cm->test_send_email_api($to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers );

    }

    /**
     * Sendgrid API exception test
     */
    public function test_sendgrid_exception_plain_content() {

        $cm = new \classes\app\emails\Custom_Mail();

        $to            = self::TEST_EMAIL;
        $from          = 'phpunit@verticalraise.com';
        $cc            = '';
        $subject       = 'PHPUnit Test';
        $html_content  = '<h1>Hello</h1><p>PHPUnit Test</p><small>2019</small>';
        $plain_content = true;
        $from_name     = 'PHPUnit';
        $headers       = '';

        $this->expectException(\SendGrid\Mail\TypeException::class);
        $cm->test_send_email_api($to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers );

    }

    /**
     * Sendgrid API bad request test
     */
    public function test_sendgrid_exception_subject() {

        $cm = new \classes\app\emails\Custom_Mail();

        $to            = self::TEST_EMAIL;
        $from          = 'phpunit@verticalraise.com';
        $cc            = '';
        $subject       = '';
        $html_content  = '<h1>Hello</h1><p>PHPUnit Test</p><small>2019</small>';
        $plain_content = 'Hello\\nPhpUnit Test\\n2019';
        $from_name     = 'PHPUnit';
        $headers       = '';


        $this->expectException(\Exception::class, null, 400);
        $cm->test_send_email_api($to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers );

    }

    /**
     * Sendgrid API bad request test
     */
    public function test_sendgrid_exception_htmlcontent() {

        $cm = new \classes\app\emails\Custom_Mail();

        $to            = self::TEST_EMAIL;
        $from          = 'phpunit@verticalraise.com';
        $cc            = '';
        $subject       = 'PHPUnit Test';
        $html_content  = '';
        $plain_content = 'Hello\\nPhpUnit Test\\n2019';
        $from_name     = 'PHPUnit';
        $headers       = '';


        $this->expectException(\Exception::class, null, 400);
        $cm->test_send_email_api($to, $from, $cc, $subject, $html_content, $plain_content, $from_name, $headers );

    }

}
