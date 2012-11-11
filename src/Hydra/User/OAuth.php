<?php
/**
 * This file is part of Hydra, the cozy RESTfull PHP5.3 micro-framework.
 *
 * @link        https://github.com/z7/hydra
 * @author      Sandu Lungu <sandu@lungu.info>
 * @package     hydra
 * @subpackage  core
 * @filesource
 * @license     http://www.opensource.org/licenses/MIT MIT
 */

namespace Hydra\User;

/**
 * An authorized and authenticated OAuth user.
 * 
 * @property mixed $id A service specific id, that identifies the user.
 * @property Service? $client A configured OAuth/OAuth2 Guzzle service client.
 */
class OAuth extends Authenticated {
}
