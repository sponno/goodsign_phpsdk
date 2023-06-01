<?php

namespace GoodSign;

class ExtraField
{
    public string $key = ""; // eg signer1 (this is the signer key
    public string $type = ""; //sign, input, date, name, label, c, c1 (checkbox group, eg group1), c* (required checkbox), read
    public string $opt = ""; // set to "?" for an option fields - only works with "inputs"
    public string $value = ""; // "x" for to check a checkbox - labels and inputs, read hae values

    //    s = scale, default 1
    //    font = default 'Helvetica' options are:
    //    'Times', 'monospace', 'courier'
    //    color = hex #ff0000 or any of the 16
    //    white, silver, gray, black, red, maroon,
    //    yellow, olive, lime, green, aqua, teal,
    //    blue, navy, fuchsia, purple
    public string $style = ""; // color:red;s:1;font:monospace (works with input type controls and labels)

    // An A4 portrait page measures 210 x 297 millimetres or 8.27 x 11.69 inches.
    //  In PostScript, it's 595 × 842 points.
    public ?float $left = null;  // tese values are in POINTS, so full left = 0, full right = 595 (item would be off the page)
    public ?float $top = null;
    public ?float $width = null;
    public ?float $height = null;
    public ?int $page = null;     // first page is page 1.

    public function setKey($key){
        $this->key = $key;
        return $this;
    }
    public function setType($type){
        $this->type = $type;
        return $this;
    }
    public function setTag($value){
        $this->value = $value;
        return $this;
    }
    public function setLeft($left){
        $this->left = $left;
        return $this;
    }
    public function setTop($top){
        $this->top = $top;
        return $this;
    }
    public function setWidth($width){
        $this->width = $width;
        return $this;
    }
    public function setHeight($height){
        $this->height = $height;
        return $this;
    }
    public function setPage($page){
        $this->page = $page;
        return $this;
    }
    public function setOpt($opt){
        $this->opt = $opt;
        return $this;
    }
    public function setStyle($style){
        $this->style = $style;
        return $this;
    }


    public function validateField():string{
        if($this->key=="" and $this->type!="label"){
            return 'All fields must have a key (signer) field set, only labels can be keyless.';
        }
        if(!isset($this->left) || !isset($this->top) || !isset($this->width) || !isset($this->height)|| !isset($this->page)){
            return 'All fields must have a page,  left, top, width and height set.';
        }
        return ""; // no issues found.
    }
}

