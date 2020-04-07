<?php

namespace Layotter\Upgrades;

use Exception;
use InvalidArgumentException;
use Layotter\Initializer;
use Layotter\Repositories\ElementRepository;
use Layotter\Repositories\ElementTypeRepository;
use Layotter\Services\ElementFieldsService;

class NewElementMigrator {

    /**
     * @var string
     */
    private $type_name;

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
     * @throws Exception
     */
    public function __construct($type, $values) {
        if (!ElementTypeRepository::type_name_exists($type) || !is_array($values)) {
            throw new InvalidArgumentException();
        }

        $this->type_name = $type;
        $this->fields = ElementFieldsService::get_fields(ElementRepository::create($type));
        $this->values = $values;
    }

    /**
     * @return int
     */
    public function migrate() {
        $id = wp_insert_post([
            'post_type' => Initializer::POST_TYPE_ELEMENT,
            'meta_input' => [
                Initializer::META_FIELD_ELEMENT_TYPE => $this->type_name
            ],
            'post_status' => 'publish'
        ]);

        $migrator = new FieldsMigrator($id, $this->fields, $this->values);
        $migrator->migrate();

        return $id;
    }
}
