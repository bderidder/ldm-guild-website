package ladanse.website.domain.phpbb;

import javax.persistence.Entity;
import javax.persistence.Table;

public class Group {
    private Integer id;
    private String name;

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }
}
