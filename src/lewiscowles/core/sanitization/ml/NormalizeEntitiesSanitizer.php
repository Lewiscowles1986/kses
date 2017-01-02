<?php

namespace lewiscowles\core\sanitization\ml;

use lewiscowles\core\validation\ValidatorInterface;
use lewiscowles\core\sanitization\SanitizerInterface;

class NormalizeEntitiesSanitizer implements SanitizerInterface {
    protected $allowedentitynames;
    protected $validator;
    
    const ALLOWED_ENTITIES = [
		'nbsp',    'iexcl',  'cent',    'pound',  'curren', 'yen',
		'brvbar',  'sect',   'uml',     'copy',   'ordf',   'laquo',
		'not',     'shy',    'reg',     'macr',   'deg',    'plusmn',
		'acute',   'micro',  'para',    'middot', 'cedil',  'ordm',
		'raquo',   'iquest', 'Agrave',  'Aacute', 'Acirc',  'Atilde',
		'Auml',    'Aring',  'AElig',   'Ccedil', 'Egrave', 'Eacute',
		'Ecirc',   'Euml',   'Igrave',  'Iacute', 'Icirc',  'Iuml',
		'ETH',     'Ntilde', 'Ograve',  'Oacute', 'Ocirc',  'Otilde',
		'Ouml',    'times',  'Oslash',  'Ugrave', 'Uacute', 'Ucirc',
		'Uuml',    'Yacute', 'THORN',   'szlig',  'agrave', 'aacute',
		'acirc',   'atilde', 'auml',    'aring',  'aelig',  'ccedil',
		'egrave',  'eacute', 'ecirc',   'euml',   'igrave', 'iacute',
		'icirc',   'iuml',   'eth',     'ntilde', 'ograve', 'oacute',
		'ocirc',   'otilde', 'ouml',    'divide', 'oslash', 'ugrave',
		'uacute',  'ucirc',  'uuml',    'yacute', 'thorn',  'yuml',
		'quot',    'amp',    'lt',      'gt',     'apos',   'OElig',
		'oelig',   'Scaron', 'scaron',  'Yuml',   'circ',   'tilde',
		'ensp',    'emsp',   'thinsp',  'zwnj',   'zwj',    'lrm',
		'rlm',     'ndash',  'mdash',   'lsquo',  'rsquo',  'sbquo',
		'ldquo',   'rdquo',  'bdquo',   'dagger', 'Dagger', 'permil',
		'lsaquo',  'rsaquo', 'euro',    'fnof',   'Alpha',  'Beta',
		'Gamma',   'Delta',  'Epsilon', 'Zeta',   'Eta',    'Theta',
		'Iota',    'Kappa',  'Lambda',  'Mu',     'Nu',     'Xi',
		'Omicron', 'Pi',     'Rho',     'Sigma',  'Tau',    'Upsilon',
		'Phi',     'Chi',    'Psi',     'Omega',  'alpha',  'beta',
		'gamma',   'delta',  'epsilon', 'zeta',   'eta',    'theta',
		'iota',    'kappa',  'lambda',  'mu',     'nu',     'xi',
		'omicron', 'pi',     'rho',     'sigmaf', 'sigma',  'tau',
		'upsilon', 'phi',    'chi',     'psi',    'omega',  'thetasym',
		'upsih',   'piv',    'bull',    'hellip', 'prime',  'Prime',
		'oline',   'frasl',  'weierp',  'image',  'real',   'trade',
		'alefsym', 'larr',   'uarr',    'rarr',   'darr',   'harr',
		'crarr',   'lArr',   'uArr',    'rArr',   'dArr',   'hArr',
		'forall',  'part',   'exist',   'empty',  'nabla',  'isin',
		'notin',   'ni',     'prod',    'sum',    'minus',  'lowast',
		'radic',   'prop',   'infin',   'ang',    'and',    'or',
		'cap',     'cup',    'int',     'sim',    'cong',   'asymp',
		'ne',      'equiv',  'le',      'ge',     'sub',    'sup',
		'nsub',    'sube',   'supe',    'oplus',  'otimes', 'perp',
		'sdot',    'lceil',  'rceil',   'lfloor', 'rfloor', 'lang',
		'rang',    'loz',    'spades',  'clubs',  'hearts', 'diams',
		'sup1',    'sup2',   'sup3',    'frac14', 'frac12', 'frac34',
		'there4',
	];
    
    public function __construct(ValidatorInterface $DataValidator, 
                                ValidatorInterface $EntityValidator, 
                                array $AllowedEntityNames = self::ALLOWED_ENTITIES) {
        $this->allowedentitynames = array_filter($AllowedEntityNames, [$EntityValidator, 'validate']);
        // unsure if should throw exception if $AllowedEntityNames differs from internal...
        // if exception is thrown ensure test case(s) added
        $this->validator = $DataValidator;
    }
    
    public function sanitize($input) {
        return $this->normalize_entities($input);
    }
    
    protected function normalize_entities($string) {
	    // Disarm all entities by converting & to &amp;
	    $string = \str_replace('&', '&amp;', $string);

	    // Change back the allowed entities in our entity whitelist
	    $string = \preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', [$this, 'named_entities'], $string);
	    $string = \preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', [$this,'numeric_entity_values'], $string);
	    $string = \preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', [$this,'hex_values'], $string);

	    return $string;
    }

    protected function named_entities($matches) {
	    if ( empty($matches[1]) ) {
		    return '';
	    }

	    $i = $matches[1];
	    return ( ! \in_array( $i, $this->allowedentitynames ) ) ? "&amp;{$i};" : "&{$i};";
    }

    protected function numeric_entity_values($matches) {
	    if ( empty($matches[1]) ) {
		    return '';
	    }

	    $i = $matches[1];
	    if ($this->validator->validate($i)) {
		    $i = \str_pad(\ltrim($i,'0'), 3, '0', STR_PAD_LEFT);
		    $i = "&#{$i};";
	    } else {
		    $i = "&amp;#{$i};";
	    }

	    return $i;
    }

    protected function hex_values($matches) {
	    if ( empty($matches[1]) ) {
		    return '';
	    }

	    $hexchars = $matches[1];
	    return ( ! $this->validator->validate( \hexdec( $hexchars ) ) ) ? "&amp;#x{$hexchars};" : '&#x'.\ltrim($hexchars,'0').';';
    }

}
