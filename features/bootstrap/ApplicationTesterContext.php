<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Dock\Containers\Container;

class ApplicationTesterContext implements Context, SnippetAcceptingContext
{
    private $container;

    const CONTAINER_ID = '12541255';
    const FAKE_DNS = 'docker.hostname';

    public function __construct()
    {
        $this->container = require __DIR__ . '/app/container.php';
    }

    /**
     * @Given I have a Docker Compose file that contains one container
     */
    public function iHaveADockerComposeFileThatContainsOneContainer()
    {
        $this->container['containers.configured_container_ids']->setIds([self::CONTAINER_ID]);
    }

    /**
     * @Given this container is running
     */
    public function thisContainerIsRunning()
    {
        $this->container['containers.container_details']->setState(
            self::CONTAINER_ID,
            Container::STATE_RUNNING,
            self::FAKE_DNS
        );
    }

    /**
     * @Given this container ran previously but is not currently running
     */
    public function thisContainerIsNotRunning()
    {
        $this->container['containers.container_details']->setState(
            self::CONTAINER_ID,
            Container::STATE_EXITED,
            self::FAKE_DNS
        );
    }

    /**
     * @When I run the :command command
     */
    public function iRunTheCommand($command)
    {
        $this->container['application_tester']->run([$command]);
    }

    /**
     * @Then I should see that this container has a status of :status
     */
    public function iShouldSeeThatThisContainerHasAStatusOf($status)
    {
        if (!preg_match('/CONTAINER_' . self::CONTAINER_ID . '.*' . preg_quote($status) . '/', $this->getApplicationOutput())) {
            throw new \Exception("Container status was not $status");
        }
    }

    /**
     * @Then I should see the DNS resolution of the container
     */
    public function iShouldSeeTheDnsResolutionOfTheContainer()
    {
        if (!preg_match('/CONTAINER_' . self::CONTAINER_ID . '.*' . preg_quote(self::FAKE_DNS) . '/', $this->getApplicationOutput())) {
            throw new \Exception("Container DNS was not displayed as " . self::FAKE_DNS);
        }
    }

    /**
     * @return string
     */
    private function getApplicationOutput()
    {
        return $this->container['application_tester']->getDisplay();
    }

    /**
     * @Then I should see that this container's logs
     */
    public function iShouldSeeThatThisContainerSLogs()
    {
        if ($this->getApplicationOutput() !== "log line for all components\n") {
            throw new \Exception('Logs for all components not displayed');
        }
    }

    /**
     * @Given I have a Docker Compose file that contains two containers
     */
    public function iHaveADockerComposeFileThatContainsTwoContainers()
    {
        throw new PendingException();
    }
}
