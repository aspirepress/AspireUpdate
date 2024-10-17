<?php

interface AspirePress_HostRewriterInterface {

	public function rewrite( $url ): string;
}
