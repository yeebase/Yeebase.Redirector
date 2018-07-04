<?php
namespace Yeebase\Redirector\Controller\Module;

/**
 * This file is part of the Yeebase.Readiness package.
 *
 * (c) 2018 yeebase media GmbH
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Response;
use Neos\Neos\Controller\Module\AbstractModuleController;
use Neos\RedirectHandler\Storage\RedirectStorageInterface;

class RedirectorController extends AbstractModuleController
{
    /**
     * @Flow\Inject
     * @var RedirectStorageInterface
     */
    protected $redirectStorage;

    /**
     *
     */
    public function indexAction()
    {
        $redirects = $this->redirectStorage->getAll();
        $this->view->assign('redirects', iterator_to_array($redirects));
    }

    /**
     *
     */
    public function newAction()
    {
        $this->view->assign('statusCodes', array_reduce([301, 307, 410], function ($mem, $statusCode) {
            $mem[$statusCode] = $statusCode . ' (' . Response::getStatusMessageByCode($statusCode) . ')';
            return $mem;
        }, []));
    }

    /**
     * @Flow\Validate(argumentName="source", type="notEmpty")
     * @Flow\Validate(argumentName="target", type="notEmpty")
     *
     * @param string $source
     * @param string $target
     * @param int $statusCode
     */
    public function createAction(string $source, string $target, int $statusCode)
    {
        $this->redirectStorage->addRedirect($source, $target, $statusCode);
        $this->addFlashMessage('Redirect created');
        $this->redirect('index');
    }

    /**
     * @param string $source
     */
    public function deleteAction(string $source)
    {
        $this->redirectStorage->removeOneBySourceUriPathAndHost($source);
        $this->addFlashMessage('Redirect deleted');
        $this->redirect('index');
    }
}
