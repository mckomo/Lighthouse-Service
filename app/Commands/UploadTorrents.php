<?php namespace Lighthouse\Commands;

use Lighthouse\Commands\Command;
use Lighthouse\Services\Torrents\Entities\Torrent;

class UploadTorrents extends Command {

    public $torrent;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Torrent $torrent)
	{
		$this->torrent = $torrent;
	}

}
