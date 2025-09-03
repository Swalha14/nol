<?php

class HelloWorld
{
    public function greet()
    {
        return "<h1> Hello ICS C!</h1><p> Today is ".date("l")."</p>";
    }

    public function today()
    {
        return "<p>Today is" .date("l")."</p>";
    }
}

$hello= new HelloWorld();
print $hello->greet();
print $hello->today();


?>
