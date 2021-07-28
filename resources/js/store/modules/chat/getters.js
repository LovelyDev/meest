import isEmpty from 'lodash/isEmpty';

export default {
  /**
   * Determines if there is an error.
   *
   * @param state
   * @returns {boolean}
   */
  hasError(state) {
    return !isEmpty(state.errorMessages);
  }
};
