<?php

namespace ArtMin96\FilamentJet\Datas;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Data;

class SessionData extends Data
{
    public string $connection;

    public string $driver;

    public string $table = 'sessions';

    public static function make(): self
    {
        $data = config('session');
        if (! is_array($data)) {
            throw new \Exception('straneg things');
        }

        return self::from($data);
    }

    public function getUserActivities(): Collection
    {
        return DB::connection($this->connection)
            ->table($this->table)
            //->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    public function getSessionsProperty(): Collection
    {
        if ($this->driver !== 'database') {
            return collect();
        }

        return $this->getUserActivities()
            ->map(
                function ($session) {
                    return (object) [
                        'agent' => $this->createAgent($session),
                        'ip_address' => $session->ip_address,
                        'is_current_device' => $session->id === request()->session()->getId(),
                        'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    ];
                }
            );
    }

    public function deleteOtherSessionRecords(): void
    {
        if ($this->driver !== 'database') {
            return;
        }
        DB::connection($this->connection)
            ->table($this->table)
            //->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('user_id', Auth::id())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return \Jenssegers\Agent\Agent
     */
    protected function createAgent($session)
    {
        return tap(new \Jenssegers\Agent\Agent(), function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}
