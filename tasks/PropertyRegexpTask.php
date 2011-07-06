<?php
require_once 'phing/Task.php';
include_once 'phing/system/util/Properties.php';

class PropertyRegexpTask extends Task {
    protected $regexp;
    protected $name;
    protected $input;
    protected $override = false;
    protected $userProperty = false;

    public function __construct() {
        $this->regexp = new Regexp();
    }

    public function setPattern($pattern) {
        $this->regexp->setPattern(str_replace('`', '\\`', $pattern));
    }

    public function getPattern() {
        return $this->regexp->getPattern();
    }

    public function setReplace($replace) {
        $this->regexp->setReplace($replace);
    }

    public function getReplace() {
        return $this->regexp->getReplace();
    }

    public function setModifiers($modifiers) {
        $this->regexp->setModifiers($modifiers);
    }

    public function getModifiers() {
        return $this->regexp->getModifiers();
    }

    public function setIgnoreCase($bit) {
        $this->regexp->setIgnoreCase($bit);
    }

    public function getIgnoreCase() {
        return $this->regexp->getIgnoreCase();
    }

    public function setMultiline($multiline) {
        $this->regexp->setMultiline($multiline);
    }

    public function getMultiline() {
        return $this->regexp->getMultiline();
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

        $input = $this->input;

        try {
            $input = $this->regexp->replace($input);
            $this->log('Performing regexp replace: /' . $this->pattern . '/' . $this->replace . '/g' . $this->modifiers, Project::MSG_VERBOSE);
        } catch (Exception $e) {
            $this->log('Error performing regexp replace: ' . $e->getMessage(), Project::MSG_WARN);
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
