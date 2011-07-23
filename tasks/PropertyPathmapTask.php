<?php
require_once 'phing/Task.php';
include_once 'phing/system/util/Properties.php';
include_once dirname(__FILE__) . '/../util/MyStringHelper.php';

class PropertyPathmapTask extends Task {
    protected $name;
    protected $input;
    protected $to;
    protected $override = false;
    protected $userProperty = false;

    public function __construct() {
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function getTo() {
        return $this->to;
    }

    public function setInput($input) {
        $this->input = $input;
    }

    public function getInput() {
        return $this->input;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setOverride($v) {
        $this->override = (boolean) $v;
    }

    public function getOverride() {
        return $this->override;
    }

    public function setUserProperty($v) {
        $this->userProperty = (boolean) $v;
    }

    public function getUserProperty() {
        return $this->userProperty;
    }

    public function main() {
        if ($this->name === null) {
            throw new BuildException("You must specify the 'name' attribute.", $this->getLocation());
        }

        if ($this->input === null) {
            throw new BuildException("You must specify the 'input' attribute.", $this->getLocation());
        }

        if ($this->to === null) {
            throw new BuildException("You must specify the 'to' attribute.", $this->getLocation());
        }

        $input = $this->input;

        try {
            $input = MyStringHelper::pathmap($input, $this->to);
            $this->log('Performing pathmap replace: ' . $this->to, Project::MSG_VERBOSE);
        } catch (Exception $e) {
            $this->log('Error performing pathmap replace: ' . $e->getMessage(), Project::MSG_WARN);
        }

        $this->addProperty($this->name, $input);
    }

    protected function addProperty($name, $value) {
        if ($this->userProperty) {
            if ($this->project->getUserProperty($name) === null || $this->override) {
                $this->project->setInheritedProperty($name, $value);
            } else {
                $this->log("Override ignored for " . $name, Project::MSG_VERBOSE);
            }
        } else {
            if ($this->override) {
                $this->project->setProperty($name, $value);
            } else {
                $this->project->setNewProperty($name, $value);
            }
        }
    }
}
