<?php

namespace lewiscowles\tests;

use lewiscowles\core\sanitization\ml\NormalizeEntitiesSanitizer;
use lewiscowles\core\validation\UnicodeValidator;
use lewiscowles\core\validation\StringValidator;
use PHPUnit\Framework\TestCase;

class TestNormalizeEntitiesSanitizer extends TestCase {

    function setup() {
        $this->sanitizer = new NormalizeEntitiesSanitizer(
            new UnicodeValidator(),
            new StringValidator()
        );
    }
    
    public function validData() {
        return [
            ['<a href="bib bob bib">&#xa110; &amp; bob</a>'],
            ['&spades;'],
            ['&sup1;'],
            ['&sup2;'],
            ['&sup3;'],
            ['&frac14;'],
            ['&frac12;'],
            ['&frac34;'],
            ['&there4;'],
            ['&#xfffd;'],
            ['&#272;'],
        ];
    }
    
    public function invalidData() {
        return [
            [
                '<a href="bib bob bib">&amp;#xzzzzz; &amp; bob</a>',
                '<a href="bib bob bib">&#xzzzzz; &amp; bob</a>'
            ],
            [
                '<a href="bib bob bib">&amp;idontexist; &amp; bob</a>',
                '<a href="bib bob bib">&idontexist; &amp; bob</a>'
            ],
            ['&amp;#9999999;','&#9999999;'],
        ];
    }
    
    public function testEmptyStringComesBackEmpty() {
        $this->assertEquals('', $this->sanitizer->sanitize('') );
    }
    
    /**
     * @dataProvider validData
     */
    public function testValidEntitiesArePreserved($val) {
        $this->assertEquals($val, $this->sanitizer->sanitize($val));
    }
    
    /**
     * @dataProvider invalidData
     */
    public function testInvalidEntitiesAreEscaped($expected, $val) {
        $this->assertEquals($expected, $this->sanitizer->sanitize($val));
    }
}
