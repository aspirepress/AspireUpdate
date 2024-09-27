<?php

interface AspirePress_PathRewriterInterface
{
    public function rewrite($url): string;
}
