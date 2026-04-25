<?php

declare(strict_types=1);

class Tag {

    public string $name;
    protected array $attribute = [];

    public function __construct( string $name ) {
        $this->name = $name; 
    }

    public function attr( string $name, string $value ) {
        $this->attribute[$name] = $value;
        return $this;
    }

    protected function attrToString() : string {
        $attributes = [];
        foreach( $this->attribute as $s => $v ){
            $attributes[] = "$s=\"$v\"";
        }
        return implode(' ', $attributes);
    }

    public function render( ) : string {
        $attributes = $this->attrToString();
        return "<{$this->name} $attributes>";
    }

}

class SingleTag extends Tag {

}

class PairTag extends Tag {

    protected array $child = [];

    public function appendChild( Tag $tag ) {
        $this->child[] = $tag;
        return $this;
    }

    public function render( ) : string {
        $attributes = $this->attrToString();
        $child = implode('', array_map(function( Tag $tag ){
           return $tag->render(); 
        }, $this->child));
        return "<{$this->name} $attributes>$child</{$this->name}>";
    }

}
 
$img = (New SingleTag('img'))->attr('src', 'fgdf.png')->attr('alt', 'image');
$label = (New PairTag('label'))->appendChild($img); 
 
$html = $label->render();
echo $html;
echo '<hr>';
echo htmlspecialchars($html);