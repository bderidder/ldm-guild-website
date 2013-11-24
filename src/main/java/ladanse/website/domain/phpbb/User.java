package ladanse.website.domain.phpbb;

import javax.persistence.Entity;
import javax.persistence.Table;
import java.util.List;

public class User {
    private Integer id;
    private String username;
    private String email;
    private List<Group> groups;
}
