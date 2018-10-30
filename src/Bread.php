<?php

namespace Bread;

class Bread
{
    protected $formfields;
    protected $breads;

    public function addFormfield($formfield)
    {
        if (gettype($formfield) == 'string') {
            $formfield = app($formfield);
        }
        $this->formfields[$formfield->name] = $formfield;

        return $this;
    }

    public function formfield($type)
    {
        return $this->formfields[$type] ?? null;
    }

    public function formfields()
    {
        return collect($this->formfields);
    }

    public function getBread($slug)
    {
<<<<<<< HEAD
        return $this->getBreads()->where('slug', $slug)->first();
=======
>>>>>>> de50ea7f5d20f13f9654bb94a36a7ba4b2cf821d
    }

    public function getBreadByTable($table)
    {
<<<<<<< HEAD
        return $this->getBreads()->where('table', $table)->first();
=======
>>>>>>> de50ea7f5d20f13f9654bb94a36a7ba4b2cf821d
    }

    public function getBreads()
    {
<<<<<<< HEAD
        if (!$this->breads) {
            $this->breads = \Cache::remember('breads', now()->addHours(24), function ()
            {
                $breads = collect();
                $files = scandir(config('bread.bread_path'));
                foreach ($files as $file) {
                    if (ends_with($file, '.json')) {
                        $bread = new BreadClass(
                            json_decode(
                                file_get_contents(config('bread.bread_path').DIRECTORY_SEPARATOR.$file)
                            )
                        );
                        $breads->push($bread);
                    }
                }

                return $breads;
            });
        }

        return $this->breads;
=======
>>>>>>> de50ea7f5d20f13f9654bb94a36a7ba4b2cf821d
    }

    public function hasBread($slug)
    {
<<<<<<< HEAD
        return !is_null($this->getBread($slug));
=======
>>>>>>> de50ea7f5d20f13f9654bb94a36a7ba4b2cf821d
    }

    public function hasBreadByTable($table)
    {
<<<<<<< HEAD
        return !is_null($this->getBreadByTable($table));
=======
>>>>>>> de50ea7f5d20f13f9654bb94a36a7ba4b2cf821d
    }

    public function saveBread($bread)
    {
<<<<<<< HEAD
        $path = config('bread.bread_path').DIRECTORY_SEPARATOR.$bread->table.'.json';
        return file_put_contents($path, json_encode($bread, JSON_PRETTY_PRINT));
=======
>>>>>>> de50ea7f5d20f13f9654bb94a36a7ba4b2cf821d
    }

    public function deleteBread($table)
    {
<<<<<<< HEAD
        $path = config('bread.bread_path').DIRECTORY_SEPARATOR.$table.'.json';
        if (!file_exists($path)) {
            return false;
        }

        return unlink($path);
=======
>>>>>>> de50ea7f5d20f13f9654bb94a36a7ba4b2cf821d
    }
}
