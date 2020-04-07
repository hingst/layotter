<?php

namespace Layotter\Upgrades;

use InvalidArgumentException;
use Layotter\Initializer;
use Layotter\Repositories\OptionsRepository;
use Layotter\Services\OptionsFieldsService;

class OptionsMigrator {

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $values;

    /**
     * @param string $type
     * @param array $values
     */
    public function __construct($type, $values) {
        if (!OptionsRepository::type_exists($type) || !is_array($values)) {
            throw new InvalidArgumentException();
        }

        $this->fields = OptionsFieldsService::get_fields(OptionsRepository::create($type));
        $this->values = $values;
    }

    /**
     * @return int
     */
    public function migrate() {
        $id = wp_insert_post([
            'post_type' => Initializer::POST_TYPE_OPTIONS,
            'post_status' => 'publish'
        ]);

        $migrator = new FieldsMigrator($id, $this->fields, $this->values);
        $migrator->migrate();

        return $id;
    }

}
