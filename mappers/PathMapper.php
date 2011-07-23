<?php
require_once 'phing/mappers/FileNameMapper.php';
require_once dirname(__FILE__) . '/../util/MyStringHelper.php';

class PathMapper implements FileNameMapper {
    protected $to;

    public function main($sourceFilename) {
        if ($this->to === null) {
            throw new BuildException('PathMapper error, 'to' attribute not set.');
        }

        return array(MyStringHelper::pathmap($sourceFilename, $this->to));
    }

    public function setFrom($from) {}

    public function setTo($to) {
        $this->to = $to;
    }
}
