<?php

namespace WCGQL\Helpers;

class State
{
    private $id;
    private $countryId;
    private $name;

    public function __construct($countryId, $stateId)
    {
        $this->id = $stateId;
        $this->countryId = $countryId;
        $allowedStates = WC()->countries->get_allowed_country_states();

        try {
            $this->name = $allowedStates[$countryId][$stateId];
        } catch (\Exception $th) {
            throw new \Exception('Invalid state: '. $th->getMessage());
        }
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getCountryId()
    {
        return $this->countryId;
    }

    public function getCode()
    {
        return $this->id;
    }

    public function toSchema()
    {
        return [
            'zone_id' => $this->id,
            'name' => $this->name,
            'code' => $this->id,
        ];
    }
}
