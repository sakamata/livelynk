<?php

namespace App\Service;

use DB;
use App\UserTable;
use App\CommunityUserStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class UserService
{
    public function PersonGet(int $community_user_id)
    {
        return 'App\UserTable'::UsersGet('community_user.id', 'asc')
            ->where('community_user.id', $community_user_id)
            ->first();
    }

    public function SelfCommunityUsersGet(string $orderkey, string $order, int $community_id)
    {
        return 'App\UserTable'::UsersGet($orderkey, $order)
            ->MyCommunity($community_id)
            ->get();
    }

    public function AllCommunityUsersGet(string $orderkey, string $order)
    {
        return 'App\UserTable'::UsersGet($orderkey, $order)
            ->get();
    }

    public function IDtoRoleGet(int $community_user_id)
    {
        return 'App\CommunityUserStatus'::IDtoRoleGet($community_user_id);
    }

    // 呼び出し元
    // AdminUserController->create
    // AdminCommunityController->create
    // InportPostController->MacAddress
    public function UserCreate(
        string $name = null,
        string $unique_name,
        string $email = null,
        bool $provisional,
        string $password,
        int $community_id,
        int $role_id,
        string $action
    ) {
        $now = Carbon::now();
        $user_id = 'App\UserTable'::insertGetId([
            'name' => $name,
            'unique_name' => $unique_name,
            'email' => $email,
            'provisional' => $provisional,
            'password' => Hash::make($password),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        // 中間tableに値を入れる
        $community_user_id = DB::table('community_user')->insertGetId([
            'community_id' => $community_id,
            'user_id' => $user_id,
        ]);
        // user status管理のtableに値を入れる
        DB::table('communities_users_statuses')->insert([
            'id' => $community_user_id,
            'role_id' => $role_id,
            'hide' => 0,
            'last_access' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if (($action == 'AdminUserCreate') || ($action == 'InportPostProvisional')) {
            return $community_user_id;
        }
        if ($action == 'AdminCommunityCreate') {
            return $user_id;
        }
    }

    // 仮ユーザー名生成 動物名+色名のランダムな組み合わせ、重複の場合は増分値を追加
    public function ProvisionalNameMaker()
    {
        $a_count = count($this->animalList()) - 1;
        $c_count = count($this->colorList()) - 1;
        $w = null;
        do {
            $a = rand(0, $a_count);
            $c = rand(0, $c_count);
            $animalList = $this->animalList();
            $colorList = $this->colorList();
            $animal = $animalList[$a];
            $color = $colorList[$c];
            $provisionalName = $color . '-' . $animal . $w;
            $exists = DB::table('users')->where('unique_name', $provisionalName)->exists();
            $w++;
        } while ($exists);
        return $provisionalName;
    }

    public function AnimalList()
    {
        return $animal = array(
            'albatross',
            'anteater',
            'armadillo',
            'baboon',
            'bactrianCamel',
            'badger',
            'bat',
            'bear',
            'beaver',
            'beluga',
            'bison',
            'boar',
            'buffalo',
            'cachalot',
            'calf',
            'camel',
            'cat',
            'cheetah',
            'chimpanzee',
            'chipmunk',
            'cobra',
            'condor',
            'cow',
            'crocodile',
            'cuckoo',
            'deer',
            'dog',
            'dolphin',
            'donkey',
            'dugong',
            'eagle',
            'elephant',
            'falcon',
            'fawn',
            'fennec',
            'flamingo',
            'foal',
            'fox',
            'frog',
            'furSeal',
            'gerbil',
            'gibbon',
            'giraffe',
            'goat',
            'goose',
            'gorilla',
            'grizzlyBear',
            'HamadryasBaboon',
            'hamster',
            'hawk',
            'hedgehog',
            'heron',
            'hippopotamus',
            'horse',
            'humpbackWhale',
            'iguana',
            'jaguar',
            'kangaroo',
            'kid',
            'kingfisher',
            'kitten',
            'kiwi',
            'koala',
            'lamb',
            'leopard',
            'lion',
            'lizard',
            'llama',
            'manatee',
            'mandrill',
            'meerkat',
            'mole',
            'mongoose',
            'monkey',
            'moose',
            'mouse',
            'mule',
            'narwhal',
            'okapi',
            'orangutan',
            'orca',
            'ostrich',
            'otter',
            'owl',
            'panda',
            'panther',
            'parrot',
            'peafowl',
            'pelican',
            'penguin',
            'polarBear',
            'pony',
            'porcupine',
            'puppy',
            'rabbit',
            'raccoon',
            'raccoonDog',
            'rattlesnake',
            'reindeer',
            'rhinoceros',
            'salamander',
            'seagull',
            'seal',
            'seaLion',
            'seaOtter',
            'serpent',
            'servalCat',
            'sheep',
            'shoebill',
            'sloth',
            'sparrow',
            'squirrel',
            'stork',
            'swallow',
            'swan',
            'tadpole',
            'tapir',
            'thoroughbred',
            'tiger',
            'turtle',
            'wagtail',
            'walrus',
            'weasel',
            'wildBoar',
            'wildCat',
            'wolf',
            'yak',
            'zebra'
        );
    }

    public function ColorList()
    {
        return $color = array(
            'beige',
            'black',
            'blue',
            'blue-green',
            'bright-yellow',
            'bronze',
            'brown',
            'chestnut',
            'maroon',
            'chocolate',
            'cinnabar-red',
            'vermilion',
            'cobalt-blue',
            'copper',
            'cream',
            'cyan',
            'dark-blue',
            'dark-gray',
            'charcoal-gray',
            'dark-green',
            'dark-red',
            'emerald-green',
            'fluorescent',
            'gold',
            'gray',
            'green',
            'greenish-brown',
            'indigo-blue',
            'deep-blue',
            'iridescent',
            'ivory',
            'khaki',
            'lavender',
            'light-blue',
            'lime',
            'madder',
            'dark-red',
            'magenta',
            'mint',
            'mustard',
            'navy-blue',
            'ochre',
            'olive',
            'opaque',
            'orange',
            'pink',
            'plum',
            'purple',
            'purple-blue',
            'violet',
            'rainbow',
            'red',
            'red-purple',
            'rose',
            'royal-blue',
            'salmon-pink',
            'sand',
            'scarlet',
            'silver',
            'sky-blue',
            'transparent',
            'turquoise',
            'turquoise-blue',
            'vanilla',
            'violet',
            'viridian',
            'white',
            'wine-red',
            'yellow',
            'yellow-green'
        );
    }
}
