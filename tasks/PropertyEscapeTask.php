<?php
require_once 'phing/Task.php';
include_once 'phing/system/util/Properties.php';

class PropertyEscapeTask extends Task {
    protected $name;
    protected $input;
    protected $delimiter;
    protected $override = false;
    protected $userProperty = false;

    public function setInput($input) {
        $this->input = $input;
    }

    public function getInput() {
        return $this->input;
    }

    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }

    public function getDelimiter() {
        return $this->delimiter;
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
        if ($this->input === null) {
            throw new BuildException("You must specify 'input' attribute.", $this->getLocation);
        }

        if ($this->name === null) {
            throw new BuildException("You must specify 'name' attribute.", $this->getLocation);
        }

        $return = preg_quote($this->input, $this->delimiter);
        $this->addProperty($this->name, $return);
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
