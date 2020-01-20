<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    const IS_ALLOW = 1;
    const IS_DES_ALLOW = 0;

    public function posts()
    {
        return $this->hasOne(Post::class);
    }

    public function author()
    {
        return $this->hasOne(Post::class);
    }

    public function allow()
    {
        $this->status = self::IS_ALLOW;
        $this->save();
    }

    public function desAllow()
    {
        $this->status = self::IS_DES_ALLOW;
        $this->save();
    }

    public function toggleStatus()
    {
        if ($this->status == null){
            return $this->allow();
        }

        return $this->$this->desAllow();
    }
}
