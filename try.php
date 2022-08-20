<?php
try
{
    $satr=9+"xdffdsf";
}
catch (\Exception $e)
{
    echo $e->getMessage()." 1";
}
catch (Throwable $e)
{
    echo $e->getMessage()." 2";
}
