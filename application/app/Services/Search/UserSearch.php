<?php namespace App\Services\Search;

use App\User;

class UserSearch implements SearchInterface {


    /**
     * Search users in local database.
     *
     * @param string  $q
     * @param int     $limit
     * @param string  $type
     *
     * @return array
     */
    public function search($q, $limit = 10)
    {
        $users = User::where('email', 'like', $q.'%')
                     ->orWhere('username', 'like', $q.'%')
                     ->select('email', 'username', 'first_name', 'last_name', 'id', 'avatar_url')
                     ->limit(20)
                     ->get();

        foreach($users as $user) {
            $user->followersCount;
        }

        return $users->toArray();
    }
}