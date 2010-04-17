<?php

/**
 *  @author      Ben XO (me@ben-xo.com)
 *  @copyright   Copyright (c) 2010 Ben XO
 *  @license     MIT License (http://www.opensource.org/licenses/mit-license.html)
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

require_once dirname(__FILE__) . '/../Unpacker.php';

class SSLAdatChunk extends SSLChunk
{
    const PROGRAM = "
    main:

    	field.    	
        	
	string:
		r1l>i_ r_b>s_
		
	int:
		r1l>i_ r_b>i_
		
	hex:
		r1l>i_ r_b>h_
		
	timestamp:
		r1l>i_ r_b>t_
		
	field: 
		# tail-recurses to gobble up all available fields. 
		# (last field exits program)
		r1l>i_ field_. field. 
		
	field1: int.	 c>rrow
	
	field2: string.  c>rfullpath
	field3: string.  c>rlocation
	field4: string.  c>rfilename
	
	field5: hex. 	 c>rFIVExUNKNOWN
	
	field6: string.  c>rtitle
	field7: string.  c>rartist
	field8: string.  c>ralbum
	field9: string.  c>rgenre
	field10: string. c>rlength
	
	field11: hex.    c>rELEVENxUNKNOWN
	field12: string. c>rTWELVExUNKNOWN
	
	field13: string. c>rbitrate
	field14: string. c>rfrequency
	field15: int. 	 c>rbpm 
	
	field16: hex. 	 c>rSIXTEENxUNKNOWN
	
	field17: string. c>rcomments
	field18: string. c>rlang
	field19: string. c>rgrouping
	
	field20: hex.    c>rTWENTY
	
	field21: string. c>rlabel
	field22: string. c>rcomposer
	field23: string. c>ryear
	field28: timestamp. c>rstarttime
	field29: timestamp. c>rendtime
	field31: int. c>rdeck
	
	field33: int. c>rTHIRTYTHREExUNKNOWN # ??? always seems to be 0
	field39: int. c>rTHIRTYNINExUNKNOWN
	field45: int. c>rplaytime
	
	field48: int. c>rsessionId
	field50: int. c>rplayedOne
	field51: hex. c>rFIFTYONExUNKNOWN
	field52: int. c>rplayedTwo
	
	field53: # always last field 
		timestamp. c>rupdatedAt
	";
    
    protected $fields = array();
    
    public function __construct($data)
    {
        parent::__construct('adat', '');
        $this->fields = $this->parse($data);
    }
    
    public function getData()
    {
        return $this->fields;
    }
    
    protected function parse($data)
    {
        $up = new Unpacker(self::PROGRAM);
        return $up->unpack($data);
    }
    
    protected function chunkDebugBody($indent=0)
    {
        $s = '';
        try
        {
            foreach($this->fields as $k => $v)
            {
                $s .= str_repeat("\t", $indent) . $k . ' => ' . $v . "\n";
            }
        }
        catch(Exception $e)
        {
            $s = str_repeat("\t", $indent) . 'Exception: ' . $e->getMessage();
        }
        return $s;
    }    
}