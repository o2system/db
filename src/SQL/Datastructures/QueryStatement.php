<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace O2System\Database\SQL\Datastructures;

// ------------------------------------------------------------------------

use O2System\Database\SQL\Abstracts\AbstractConnection;

/**
 * Class QueryStatement
 *
 * @package O2System\Database\SQL\Datastructures
 */
class QueryStatement
{
    /**
     * QueryStatement::$sqlStatement
     *
     * The SQL Statement.
     *
     * @var string
     */
    private $sqlStatement;

    /**
     * QueryStatement::$sqlBinds
     *
     * The SQL Statement bindings.
     *
     * @var array
     */
    private $sqlBinds = [];


    /**
     * QueryStatement::$sqlFinalStatement
     *
     * The compiled SQL Statement with SQL Statement binders.
     *
     * @var string
     */
    private $sqlFinalStatement;

    /**
     * QueryStatement::$startExecutionTime
     *
     * The start time in seconds with microseconds
     * for when this query was executed.
     *
     * @var float
     */
    private $startExecutionTime;

    /**
     * QueryStatement::$endExecutionTime
     *
     * The end time in seconds with microseconds
     * for when this query was executed.
     *
     * @var float
     */
    private $endExecutionTime;

    /**
     * QueryStatement::$affectedRows
     *
     * The numbers of affected rows.
     *
     * @var int
     */
    private $affectedRows;

    /**
     * QueryStatement::$lastInsertId
     *
     * The last insert id.
     *
     * @var mixed
     */
    private $lastInsertId;

    /**
     * QueryStatement::$error
     *
     * The query execution error info.
     *
     * @var array
     */
    private $error;

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setStatement
     *
     * Sets the raw query string to use for this statement.
     *
     * @param string $sqlStatement The SQL Statement.
     * @param array  $sqlBinds     The SQL Statement bindings.
     *
     * @return static
     */
    public function setSqlStatement( $sqlStatement, array $sqlBinds = [] )
    {
        $this->sqlStatement = $sqlStatement;
        $this->sqlBinds = $sqlBinds;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setBinds
     *
     * Will store the variables to bind into the query later.
     *
     * @param array $sqlBinds
     *
     * @return static
     */
    public function setBinds( array $sqlBinds )
    {
        $this->sqlBinds = $sqlBinds;

        return $this;
    }

    //--------------------------------------------------------------------

    public function getBinds()
    {
        return $this->sqlBinds;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setDuration
     *
     * Records the execution time of the statement using microtime(true)
     * for it's start and end values. If no end value is present, will
     * use the current time to determine total duration.
     *
     * @param int      $start
     * @param int|null $end
     *
     * @return static
     */
    public function setDuration( $start, $end = null )
    {
        $this->startExecutionTime = $start;

        if ( is_null( $end ) ) {
            $end = microtime( true );
        }

        $this->endExecutionTime = $end;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getStartExecutionTime
     *
     * Returns the start time in seconds with microseconds.
     *
     * @param bool $numberFormat
     * @param int  $decimals
     *
     * @return mixed
     */
    public function getStartExecutionTime( $numberFormat = false, $decimals = 6 )
    {
        if ( ! $numberFormat ) {
            return $this->startExecutionTime;
        }

        return number_format( $this->startExecutionTime, $decimals );
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getExecutionDuration
     *
     * Returns the duration of this query during execution, or null if
     * the query has not been executed yet.
     *
     * @param int $decimals The accuracy of the returned time.
     *
     * @return mixed
     */
    public function getExecutionDuration( $decimals = 6 )
    {
        return number_format( ( $this->endExecutionTime - $this->startExecutionTime ), $decimals );
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setErrorInfo
     *
     * Stores the occurred error information when the query was executed.
     *
     * @param int    $errorCode
     * @param string $errorMessage
     *
     * @return static
     */
    public function setError( $errorCode, $errorMessage )
    {
        $this->error[ $errorCode ] = $errorMessage;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getErrorCode
     *
     * Get the query error information.
     *
     * @return bool|int Returns FALSE when there is no error.
     */
    public function getErrorCode()
    {
        if ( $this->hasError() ) {
            return key( $this->error );
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::hasError
     *
     * Check if the latest query execution has an error.
     *
     * @return bool
     */
    public function hasError()
    {
        return ! empty( $this->error );
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getErrorMessage
     *
     * Get the query error information.
     *
     * @return bool|string Returns FALSE when there is no error.
     */
    public function getErrorMessage()
    {
        if ( $this->hasError() ) {
            return (string)reset( $this->error );
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setAffectedRows
     *
     * Sets numbers of affected rows.
     *
     * @param int $affectedRows Numbers of affected rows,
     */
    public function setAffectedRows( $affectedRows )
    {
        $this->affectedRows = $affectedRows;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getAffectedRows
     *
     * Gets numbers of affected rows.
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setAffectedRows
     *
     * Sets numbers of affected rows.
     *
     * @param int $affectedRows Numbers of affected rows,
     */
    public function setLastInsertId( $affectedRows )
    {
        $this->affectedRows = $affectedRows;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getAffectedRows
     *
     * Gets numbers of affected rows.
     *
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->affectedRows;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::isWriteStatement
     *
     * Determines if the SQL statement is a write-syntax query or not.
     *
     * @return bool
     */
    public function isWriteStatement()
    {
        return (bool)preg_match(
            '/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i',
            $this->sqlStatement
        );
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::replacePrefix
     *
     * Replace all table prefix with new prefix.
     *
     * @param string $search
     * @param string $replace
     *
     * @return mixed
     */
    public function swapTablePrefix( $search, $replace )
    {
        $sql = empty( $this->sqlFinalStatement ) ? $this->sqlStatement : $this->sqlFinalStatement;

        $this->sqlFinalStatement = preg_replace( '/(\W)' . $search . '(\S+?)/', '\\1' . $replace . '\\2', $sql );

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getSqlStatement
     *
     * Get the original SQL statement.
     *
     * @return string   The SQL statement string.
     */
    public function getSqlStatement()
    {
        return $this->sqlStatement;
    }

    //--------------------------------------------------------------------

    public function setSqlFinalStatement( $finalStatement )
    {
        $this->sqlFinalStatement = $finalStatement;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getSqlFinalStatement
     *
     * Returns the final, processed query string after binding, etal
     * has been performed.
     *
     * @return string
     */
    public function getSqlFinalStatement()
    {
        return $this->sqlFinalStatement;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::__toString
     *
     * Convert this query into compiled SQL Statement string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getFinalStatement();
    }
}