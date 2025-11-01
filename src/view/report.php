<form method="GET">
  <label for="report_type">Report type:</label>
  <select name="report_type" id="report_type">
    <option value="blog_post_count">Posts per user</option>
    <option value="user_count_by_role">Number of users by type</option>
  </select>
  <input type="hidden" name="action" value="report">
  <input type="submit" value="Generate Report">
</form>
