<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2010-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit_Selenium
 * @author     Giorgio Sironi <giorgio.sironi@asp-poli.it>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.1.2
 */

/**
 * Tests for PHPUnit_Extensions_SeleniumTestCase.
 *
 * @package    PHPUnit_Selenium
 * @author     Giorgio Sironi <giorgio.sironi@asp-poli.it>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.2.0
 */
class Extensions_Selenium2TestCaseFailuresTest extends PHPUnit_Extensions_Selenium2TestCase
{
    public function setUp()
    {
        if (version_compare(phpversion(), '5.3.0', '<')) {
            $this->markTestSkipped('Functionality available only under PHP 5.3.');
        }
        $this->setHost(PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_HOST);
        $this->setPort((int)PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_PORT);
        $this->setBrowser(PHPUNIT_TESTSUITE_EXTENSION_SELENIUM2_BROWSER);
        if (!defined('PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_TESTS_URL')) {
            $this->markTestSkipped("You must serve the selenium-1-tests folder from an HTTP server and configure the PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_TESTS_URL constant accordingly.");
        }
        $this->setBrowserUrl(PHPUNIT_TESTSUITE_EXTENSION_SELENIUM_TESTS_URL);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testInexistentCommandCausesTheTestToFail()
    {
        $this->inexistentSessionCommand();
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionsAreReThrownOnNotSuccessfulTests()
    {
        $this->onNotSuccessfulTest(new DomainException);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInexistentElementCausesTheTestToFail()
    {
        $this->url('html/test_open.html');
        $this->byId('notExistent');
    }

    public function testStaleElementsCannotBeAccessed()
    {
        $this->url('html/test_element_selection.html');
        $div = $this->byId('theDivId');
        $this->url('html/test_element_selection.html');
        try {
            $div->text();
            $this->fail('The element shouldn\'t be accessible.');
        } catch (RuntimeException $e) {
            $this->assertContains('http://seleniumhq.org/exceptions/stale_element_reference.html', $e->getMessage());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSelectObjectsCanOnlyBeCreatedOverSelectTags()
    {
        $this->url('html/test_element_selection.html');
        $div = $this->byId('theDivId');
        $select = $this->select($div);
    }
}
