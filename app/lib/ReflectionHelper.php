<?php
namespace app\lib;

class ReflectionHelper
{
    public static function getPropertiesArray($class): array
    {
        $array = [];
        try {
            $ref = new \ReflectionClass($class);
            $props = $ref->getProperties();
            foreach ($props as $prop) {
                // allow only public non-static:
                if (!$prop->isPublic() || $prop->isStatic()) continue;
                $array[] = $prop->getName();
            }
        } catch (\ReflectionException $e) {
            return ['error' => $e->getMessage()];
        }
        return $array;
    }

    public static function getDataArray($object): array
    {
        try {
            $class = new \ReflectionClass(get_class($object));
        } catch (\ReflectionException $e) {
            return ['error' => $e->getMessage()];
        }

        $array = array();
        $props = $class->getProperties();
        foreach ($props as $prop) {
            // allow only public non-static:
            if (!$prop->isPublic() || $prop->isStatic()) continue;
            $array[$prop->getName()] = $prop->getValue($object);
        }
        return $array;
    }
}