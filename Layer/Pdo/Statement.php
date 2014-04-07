<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Database\Exception
 */
-> import('Database.Exception')

/**
 * \Hoa\Database\IDal\WrapperStatement
 */
-> import('Database.IDal.WrapperStatement');

}

namespace Hoa\Database\Layer\Pdo {

/**
 * Class \Hoa\Database\Layer\Pdo\Statement.
 *
 * Wrap PDOStatement.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @author     Raphaël Emourgeon <raphael.emourgeon@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin, Raphaël Emourgeon.
 * @license    New BSD License
 */

class Statement implements \Hoa\Database\IDal\WrapperStatement {

    /**
     * The statement instance.
     *
     * @var \PDOStatement object
     */
    protected $_statement = null;

    /**
     * The iterator cache.
     *
     * @var Array object
     */
    protected $_cache = Array();

    /**
     * The current position of the iterator.
     *
     * @var Array object
     */
    protected $_next = null;



    /**
     * Create a statement instance.
     *
     * @access  public
     * @param   \PDOStatement  $statement    The PDOStatement instance.
     * @return  void
     */
    public function __construct ( \PDOStatement $statement ) {

        $this->setStatement($statement);

        return;
    }

    /**
     * Set the statement instance.
     *
     * @access  protected
     * @param   \PDOStatement  $statement    The PDOStatement instance.
     * @return  \PDOStatement
     */
    protected function setStatement ( \PDOStatement $statement ) {

        $old              = $this->_statement;
        $this->_statement = $statement;

        return $old;
    }

    /**
     * Get the statement instance.
     *
     * @access  protected
     * @return  \PDOStatement
     */
    protected function getStatement ( ) {

        return $this->_statement;
    }

    /**
     * Execute a prepared statement.
     *
     * @access  public
     * @param   array   $bindParameters    Bind parameters values if bindParam
     *                                     is not called.
     * @return  \Hoa\Database\Pdo\Statement
     * @throw   \Hoa\Database\Exception
     */
    public function execute ( Array $bindParameters = null ) {

        if(false === $this->getStatement()->execute($bindParameters))
            throw new \Hoa\Database\Exception(
                '%3$s (%1$s/%2$d)', 0, $this->errorInfo());

        return $this;
    }

    /**
     * Bind a parameter to te specified variable name.
     *
     * @access  public
     * @param   mixed   $parameter    Parameter name.
     * @param   mixed   $value        Parameter value.
     * @param   int     $type         Type of value.
     * @param   int     $length       Length of data type.
     * @return  bool
     * @throw   \Hoa\Database\Exception
     */
    public function bindParameter ( $parameter, &$value, $type = null,
                                    $length = null ) {

        if(null === $type)
            return $this->getStatement()->bindParam($parameter, $value);

        if(null === $length)
            return $this->getStatement()->bindParam($parameter, $value, $type);

        return $this->getStatement()->bindParam($parameter, $value, $type, $length);
    }

    /**
     * Return an array containing all of the result set rows.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Database\Exception
     */
    public function fetchAll ( ) {
        if(count($this->_cache) == 0) {
            $this->_cache = $this->getStatement()->fetchAll(\PDO::FETCH_ASSOC);
        }
        return $this->_cache;
    }

    /**
     * Return a single column from the next row of the result set or false if
     * there is no more row.
     *
     * @access  public
     * @param   int  $column    Column index.
     * @return  mixed
     * @throw   \Hoa\Database\Exception
     */
    public function fetchColumn ( $column = 0 ) {

        return $this->getStatement()->fetchColumn($column);
    }

    /**
     * Close the cursor, enabling the statement to be executed again.
     *
     * @access  public
     * @return  bool
     * @throw   \Hoa\Database\Exception
     */
    public function closeCursor ( ) {

        return $this->getStatement()->closeCursor();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement
     * handle.
     *
     * @access  public
     * @return  string
     * @throw   \Hoa\Database\Exception
     */
    public function errorCode ( ) {

        return $this->getStatement()->errorCode();
    }

    /**
     * Fetch extends error information associated with the last operation on the
     * statement handle.
     *
     * @access  public
     * @return  array
     * @throw   \Hoa\Database\Exception
     */
    public function errorInfo ( ) {

        return $this->getStatement()->errorInfo();
    }

    // Iterator implementation


    /**
     * Rewind the Iterator to the first element
     *
     * @access  public
     * @return  void
     */
    public function rewind( ) {

        reset($this->_cache);
    }

    /**
     * Checks if current position is valid
     *
     * @access  public
     * @return  boolean
     */
    public function valid( ) {

        return (FALSE !== $this->_next);
    }

    /**
     * Return the current element
     *
     * @access  public
     * @return  mixed
     */
    public function current( ) {

        return $this->_next[1];
    }

    /**
     * Return the key of the current element
     *
     * @access  public
     * @return  mixed
     */
    public function key( ) {

        return $this->_next[0];
    }

    /**
     * Move forward to next element
     *
     * @access  public
     * @return  void
     */
    public function next( ) {

        // Try to get the next element in our data cache.
        $this->_next = each($this->_cache);

        // Past the end of the data cache
        if (FALSE === $this->_next)
        {
            // Fetch the next row of data
            $row = $this->getStatement()->fetch(\PDO::FETCH_ASSOC);

            // Fetch successful
            if ($row)
            {
                // Add row to data cache
                $this->_cache[] = $row;
            }

            $this->_next = each($this->_cache);
        }
    }
}

}
