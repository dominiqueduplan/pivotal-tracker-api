<?php
/**
 * This file is part of the PivotalTracker API component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license LGPL v3 license <http://www.gnu.org/licenses/lgpl>
 */

namespace PivotalTrackerV5;

/**
 * Simple Pivotal Tracker api client.
 *
 * This class is loosely based on the code from Joel Dare's PHP Pivotal Tracker
 * Class: https://github.com/codazoda/PHP-Pivotal-Tracker-Class
 */
class Client
{
    /**
     * Base url for the PivotalTracker service api.
     */
    const API_URL = 'https://www.pivotaltracker.com/services/v5';

    /**
     * Name of the context project.
     *
     * @var string
     */
    private $project;

    /**
     * Used client to perform rest operations.
     *
     * @var \PivotalTracker\Rest\Client
     */
    private $client;
    /**
     * 
     * @param string $apiKey  API Token provided by PivotalTracking
     * @param string $project Project ID
     */
    public function __construct( $apiKey, $project = null )
    {
        $this->client = new Rest\Client( self::API_URL );
        $this->client->addHeader( 'Content-type', 'application/json' );
        $this->client->addHeader( 'X-TrackerToken',  $apiKey );
        $this->project = $project;
    }
    
    /**
     * Change current project ID
     * @param int $project ID of the project
     */
    public function setProject($project) {
        $this->project = $project;
    }
    
    /**
     * Returns information from the user's profile plus the list of projects to which the user has access.
     * @return object
     */
    public function me() {
        return json_decode(
            $this->client->get(
                "/me"
            )
        );
    }
    
    /**
     * Return list of the notifications for the authenticated person. Response is sorted by notification created_at, most recent first.
     * @return object
     */
    public function getMyNotifications() {
        return json_decode(
            $this->client->get(
                "/my/notifications"
            )
        );
    }

 
    /**
     * Adds a new story to PivotalTracker and returns the newly created story
     * object.
     *
     * @param array $story
     * @param string $name
     * @param string $description
     * @return object or null if project is null
     */
    public function addStory( array $story  )
    {
        if ($this->project == null) return null;
        return json_decode(
            $this->client->post(
                "/projects/{$this->project}/stories",
                json_encode( $story )
            )
        );
    }

    /**
     * Adds a new task with <b>$description</b> to the story identified by the
     * given <b>$storyId</b>.
     *
     * @param integer $storyId
     * @param string $description
     * @return \SimpleXMLElement
     */
    public function addTask( $storyId, $description )
    {
        if ($this->project == null) return null;
        return simplexml_load_string(
            $this->client->post(
                "/projects/{$this->project}/stories/$storyId/tasks",
                json_encode( array( 'description' => $description ) )
                
            )
        );
    }

    /**
     * Adds the given <b>$labels</b> to the story identified by <b>$story</b>
     * and returns the updated story instance.
     *
     * @param integer $storyId
     * @param array $labels
     * @return object
     */
    public function addLabels( $storyId, array $labels )
    {
        if ($this->project == null) return null;
        return json_decode(
            $this->client->put(
                "/projects/{$this->project}/stories/$storyId",
                json_encode(  $labels )
            )
        );
    }

    /**
     * Returns all stories for the context project.
     *
     * @param array $filter
     * @return object
     */
    public function getStories( $filter = null )
    {
        if ($this->project == null) return null;
        return json_decode(
            $this->client->get(
                "/projects/{$this->project}/stories",
                $filter ? array( 'filter' => $filter ) : null
            )
        );
    }
    
    /**
     * Fetch the content of the specified project.
     * @param int $id The ID of the project.
     * @return object
     */
    public function getProject($id)
    {
        return json_decode(
            $this->client->get(
                "/projects/$id"
            )
        );

    }
    
    /**
     * Return a set of iterations from the project. (Paginated)
     * @param int $id The ID of the project.
     * @param array $filter query parameters
     * @return object
     */
    public function getProjectIteractions($id, $filter = null)
    {
        $url = "/projects/$id/iterations?";
        $url .= $filter && array_walk($filter, array($this, 'explodeParams')) ? implode("&", $filter) : "";
        return json_decode(
            $this->client->get(
                $url
            )
        );

    }
    
    /**
     * Return new item for query filter
     * @param string $item
     * @param type $key
     */
    private function explodeParams(&$item, $key) {
        $item = $key."=".$item;
    }

    /**
     * Returns a list of projects for the currently authenticated user.
     *
     * @return object
     */
    public function getProjects()
    {
        return json_decode(
            $this->client->get(
                "/projects"
            )
        );

    }

     
}
