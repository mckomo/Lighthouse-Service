<?php

namespace Lighthouse\Services\Torrents\Contracts;

use Lighthouse\Services\Torrents\Entities\Query;
use Lighthouse\Services\Torrents\Entities\Torrent;

interface Repository
{
    /**
     * @param Query $query
     *
     * @return Torrent[]
     */
    public function search(Query $query);

    /**
     * @param Torrent $torrent
     *
     * @return bool
     */
    public function store(Torrent $torrent);

    /**
     * @param string $hash
     *
     * @return Torrent
     */
    public function get($hash);
}
