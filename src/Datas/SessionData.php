<?php

namespace ArtMin96\FilamentJet\Datas;

use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Spatie\LaravelData\Data;

final class SessionData extends Data
{
    public string $connection;

    public string $driver;

    public string $table = 'sessions';

    public static function make(): self
    {
        $data = config('session');
        if (! is_array($data)) {
            throw new Exception('straneg things');
        }

        return self::from($data);
    }

    public function getUserActivities(): Collection
    {
        return DB::connection($this->connection)
            ->table($this->table)
            //->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('user_id', auth()->id())
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
<<<<<<< HEAD
                fn($session) => (object) [
=======
                fn ($session) => (object) [
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
                    'agent' => $this->createAgent($session),
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ]
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
            ->where('user_id', auth()->id())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @return Agent
     */
<<<<<<< HEAD
    private function createAgent(mixed $session)
    {
        return tap(new Agent, static function ($agent) use ($session) : void {
=======
    protected function createAgent(mixed $session)
    {
        return tap(new Agent, function ($agent) use ($session): void {
>>>>>>> d2abb10143a78f54643890ce9d627c88f47f59a0
            $agent->setUserAgent($session->user_agent);
        });
    }
}
