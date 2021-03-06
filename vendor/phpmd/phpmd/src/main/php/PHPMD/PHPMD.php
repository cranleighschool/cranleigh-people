<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD;

/**
 * This is the main facade of the PHP PMD application.
 */
class PHPMD
{
    /**
     * The current PHPMD version.
     */
    const VERSION = '@package_version@';

    /**
     * List of valid file extensions for analyzed files.
     *
     * @var array(string)
     */
    private $fileExtensions = ['php', 'php3', 'php4', 'php5', 'inc'];

    /**
     * List of exclude directory patterns.
     *
     * @var array(string)
     */
    private $ignorePatterns = ['.git', '.svn', 'CVS', '.bzr', '.hg', 'SCCS'];

    /**
     * The input source file or directory.
     *
     * @var string
     */
    private $input;

    /**
     * This property will be set to <b>true</b> when an error or a violation
     * was found in the processed source code.
     *
     * @var bool
     * @since 0.2.5
     */
    private $violations = false;

    /**
     * Additional options for PHPMD or one of it's parser backends.
     *
     * @var array
     * @since 1.2.0
     */
    private $options = [];

    /**
     * This method will return <b>true</b> when the processed source code
     * contains violations.
     *
     * @return bool
     * @since 0.2.5
     */
    public function hasViolations()
    {
        return $this->violations;
    }

    /**
     * Returns the input source file or directory path.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Returns an array with valid php source file extensions.
     *
     * @return string[]
     * @since 0.2.0
     */
    public function getFileExtensions()
    {
        return $this->fileExtensions;
    }

    /**
     * Sets a list of filename extensions for valid php source code files.
     *
     * @param array<string> $fileExtensions Extensions without leading dot.
     * @return void
     */
    public function setFileExtensions(array $fileExtensions)
    {
        $this->fileExtensions = $fileExtensions;
    }

    /**
     * Returns an array with string patterns that mark a file path as invalid.
     *
     * @return string[]
     * @since 0.2.0
     */
    public function getIgnorePattern()
    {
        return $this->ignorePatterns;
    }

    /**
     * Sets a list of ignore patterns that is used to exclude directories from
     * the source analysis.
     *
     * @param array<string> $ignorePatterns List of ignore patterns.
     * @return void
     */
    public function setIgnorePattern(array $ignorePatterns)
    {
        $this->ignorePatterns = array_merge(
            $this->ignorePatterns,
            $ignorePatterns
        );
    }

    /**
     * Returns additional options for PHPMD or one of it's parser backends.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets additional options for PHPMD or one of it's parser backends.
     *
     * @param array $options Additional backend or PHPMD options.
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * This method will process all files that can be found in the given input
     * path. It will apply rules defined in the comma-separated <b>$ruleSets</b>
     * argument. The result will be passed to all given renderer instances.
     *
     * @param string $inputPath
     * @param string $ruleSets
     * @param \PHPMD\AbstractRenderer[] $renderers
     * @param \PHPMD\RuleSetFactory $ruleSetFactory
     * @return void
     */
    public function processFiles(
        $inputPath,
        $ruleSets,
        array $renderers,
        RuleSetFactory $ruleSetFactory
    ) {

        // Merge parsed excludes
        $this->setIgnorePattern($ruleSetFactory->getIgnorePattern($ruleSets));

        $this->input = $inputPath;

        $report = new Report();

        $factory = new ParserFactory();
        $parser = $factory->create($this);

        foreach ($ruleSetFactory->createRuleSets($ruleSets) as $ruleSet) {
            $parser->addRuleSet($ruleSet);
        }

        $report->start();
        $parser->parse($report);
        $report->end();

        foreach ($renderers as $renderer) {
            $renderer->start();
        }

        foreach ($renderers as $renderer) {
            $renderer->renderReport($report);
        }

        foreach ($renderers as $renderer) {
            $renderer->end();
        }

        $this->violations = ! $report->isEmpty();
    }
}
